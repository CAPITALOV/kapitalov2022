<?php

/**
 * Класс для действий Moderator
 *
 */

namespace app\controllers;

use app\controllers\BaseController;
use app\models\ModerationManager;
use app\models\ModerationObject;
use app\models\ModeratorAction;
use app\models\Translator as T;
use app\models\User;
use Yii;
use yii\bootstrap\Progress;
use yii\data\ActiveDataProvider;
use yii\data\Sort;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * Класс для действий Moderator
 *
 */
class ModeratorController extends BaseController
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow'         => true,
                        'roles'         => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            if (!Yii::$app->user->isGuest) {
                                return Yii::$app->user->identity->hasRole(User::ROLE_MODERATOR);
                            }

                            return false;
                        },
                    ],
                ],
            ],
        ];
    }

    public function actionWork()
    {
        $mm = (new ModerationManager())->setReturnQueryInstance(true);
        $limit = 20;

        $hasExistingObjects = false;

        $criteria = [
            'existing'  => true,
            'moderator' => Yii::$app->user->identity->id
        ];


        if (0 < $objectsCount = $mm->findModerationObjectsByCriteria($criteria)->count()) {
            $objectsQuery = $mm->findModerationObjectsByCriteria($criteria);
            $hasExistingObjects = true;
        }
        elseif ($limit = Yii::$app->request->get('l')) {
            /*  lock tasks by moderator */
            $criteria['limit'] = $limit;
            $criteria['existing'] = false;
            $criteria['lock'] = true;
            $objectsQuery = $mm->findModerationObjectsByCriteria($criteria);
            $this->redirect(Url::to(['moderator/work']));
        }
        else {
            $objectsQuery = (new Query())->select('*')->from(ModerationObject::TABLE)->where('id<0');
        }

        $sort = new Sort([
            'attributes' => [
                'p' => [
                    'label'   => T::t('Priority'),
                    'asc'     => ['priority' => SORT_ASC],
                    'desc'    => ['priority' => SORT_DESC],
                    'default' => SORT_DESC,
                ],
                'd' => [
                    'label'   => T::t('Time to moderation end'),
                    'asc'     => ['task_deadline' => SORT_ASC],
                    'desc'    => ['task_deadline' => SORT_DESC],
                    'default' => SORT_DESC,
                ],
                'r' => [
                    'label'   => T::t('Reward'),
                    'asc'     => ['task_price' => SORT_ASC,],
                    'desc'    => ['task_price' => SORT_DESC],
                    'default' => SORT_DESC,
                ]
            ]
        ]);

        $gridParams = [
            'dataProvider' => new ActiveDataProvider([
                'query'      => $objectsQuery->orderBy($sort->orders),
                'pagination' => [
                    'pageSize' => $limit,
                ],
            ]),
            'columns'      => [
                'id:text:id',
                [
                    'label'   => T::t('Category'),
                    'content' => function ($model) {
                        $type = (new ModerationObject)->getHumanReadableObjectType($model['type']);

                        return Html::a(Html::encode(($model['mod_category'] == ModerationObject::MODERATION_CATEGORY_VIOLATION ? T::t('category_' . $model['mod_category']) . ' / ' : '') . T::t('obj_' . $type)), Url::to([
                            'moderator/view_object',
                            'pid'  => $model['id'],
                            'id'   => $model['foreign_key'],
                            'type' => $type
                        ]), [
                            'class'    => 'js-mod-btn',
                            'data-id'  => $model['foreign_key'],
                            'data-pid' => $model['id'],
                            'data-t'   => $type,
                        ]);
                    },
                ],
                [
                    'label'   => T::t('By user'),
                    'content' => function ($model) {
                        return Html::a(Html::encode($model['nickname']), Url::to([
                                    'admin/users_view',
                                    'id' => $model['user_id']
                                ]));
                    },
                ],
                [
                    'header'  => $sort->link('p'),
                    'content' => function ($model) {
                        if (count($model) == 1) return null;

                        return Progress::widget(['percent' => $model['priority']]);
                    },
                ],
                [
                    'header'  => $sort->link('d'),
                    'content' => function ($model) {
                        return Html::tag('span', '...', [
                            'class'   => 'otime text-info',
                            'title'   => $model['task_deadline'],
                            'data-ts' => strtotime($model['task_deadline'])
                        ]);
                    },
                ],
                [
                    'label'   => T::t('Action'),
                    'content' => function ($model) {
                        $type = (new ModerationObject)->getHumanReadableObjectType($model['type']);
                        $url = Url::to([
                                'moderator/view_object',
                                'pid'  => $model['id'],
                                'id'   => $model['foreign_key'],
                                'type' => $type
                            ]);

                        return Html::a(T::t('Moderate'), $url, [
                            'class'    => 'btn btn-default js-mod-btn',
                            'data-id'  => $model['foreign_key'],
                            'data-pid' => $model['id'],
                            'data-t'   => $type
                        ]);
                    }
                ],
                [
                    'attribute' => 'task_price',
                    'header'    => $sort->link('r'),
                    'content'   => function ($model) {
                        return Html::tag('span', $model['task_price'], ['class' => 'label label-info']);
                    }
                ]
            ],
        ];


        return $this->render('work', [
            'gridParams'         => $gridParams,
            'hasExistingObjects' => $hasExistingObjects,
            'objectsCount'       => $objectsCount,
            'totalCount'         => $mm->getExpiredNonExpiredCounters()[0]
        ]);
    }

    /**
     *
     * @param int $id Moderator id
     */
    public function actionHistory($id)
    {
        $sort = new Sort([
            'attributes' => [
                'action'     => [
                    'label'   => T::t('Action'),
                    'asc'     => ['action' => SORT_ASC],
                    'desc'    => ['action' => SORT_DESC],
                    'default' => SORT_ASC,
                ],
                'created'    => [
                    'label'   => T::t('Date'),
                    'asc'     => [
                        'created' => SORT_ASC,
                        'action'  => SORT_ASC
                    ],
                    'desc'    => [
                        'created' => SORT_DESC,
                        'action'  => SORT_ASC
                    ],
                    'default' => SORT_DESC,
                ],
                'task_price' => [
                    'label'   => T::t('Reward'),
                    'asc'     => [
                        'task_price' => SORT_ASC,
                        'action'     => SORT_ASC
                    ],
                    'desc'    => [
                        'task_price' => SORT_DESC,
                        'action'     => SORT_ASC
                    ],
                    'default' => SORT_DESC,
                ]
            ]
        ]);
        $gridOpts = [
            'dataProvider' => new ActiveDataProvider([
                'query'      => (new ModerationManager)->setReturnQueryInstance(true)->getStatisticsByCriteria(['user_id' => $id])->orderBy($sort->orders),
                'pagination' => [
                    'pageSize'  => 15,
                    'pageParam' => 'users-page',
                ],
            ]),
            'filterModel'  => new ModeratorAction(),
            'columns'      => [
                'id:text:#',
                [
                    'label'     => T::t('Object'),
                    'filter'    => array_map(function ($_) {
                        return T::T('obj_' . $_);
                    }, array_flip(ModerationObject::$types)),
                    'attribute' => 'object_id',
                    'content'   => function ($model) {
                        $type = (new ModerationObject)->getHumanReadableObjectType($model['type']);

                        return Html::a(Html::encode(T::t('obj_' . $type)), Url::to([
                            'moderator/view_object',
                            'pid'  => $model['pid'],
                            'id'   => $model['foreign_key'],
                            'type' => $type,
                        ]), [
                            'class'    => 'js-mod-btn',
                            'data-id'  => $model['foreign_key'],
                            'data-pid' => $model['id'],
                            'data-t'   => $type,
                        ]);
                    },
                ],
                [
                    'header'    => $sort->link('action'),
                    'attribute' => 'action',
                    'filter'    => [
                        ModeratorAction::ACTION_APPROVE      => T::t('Blocked'),
                        ModeratorAction::ACTION_DISAPPROVE   => T::t('Ignored'),
                        ModeratorAction::ACTION_PUT_TO_QUEUE => T::t('Declined'),
                    ],
                    'content'   => function ($model) {
                        switch ($model['action']) {
                            case ModeratorAction::ACTION_APPROVE:
                                return Html::tag('span', T::t('Blocked'), ['class' => 'label label-danger']);
                                break;
                            case ModeratorAction::ACTION_DISAPPROVE:
                                return Html::tag('span', T::t('Ignored'), ['class' => 'label label-info']);
                                break;
                            case ModeratorAction::ACTION_PUT_TO_QUEUE:
                                return Html::tag('span', T::t('Declined'), ['class' => 'label label-default']);
                                break;

                            default:
                                return T::t('Unknown action');
                                break;
                        }
                    }
                ],
                [
                    'header'    => $sort->link('created'),
                    'attribute' => 'created',
                    'format'    => [
                        'date',
                        'php:Y-m-d H:i:s'
                    ],
                ],
                [
                    'header'  => $sort->link('task_price'),
                    'content' => function ($model) {
                        if (ModeratorAction::ACTION_PUT_TO_QUEUE == $model['action']) return 0;
                        else
                            return Html::tag('span', $model['action'], ['class' => 'label label-info']);
                    }
                ],
            ]
        ];
        if (Yii::$app->request->getIsGet() && Yii::$app->request->get($gridOpts['filterModel']->formName())) $gridOpts['dataProvider'] = $gridOpts['filterModel']->search(Yii::$app->request->get(), $gridOpts['dataProvider']->query);

        return $this->render('history', [
            'gridOpts'  => $gridOpts,
            'moderator' => User::findIdentity($id),
        ]);
    }

    public function actionProfile()
    {
        return $this->render('profile', [
            'balls' => (new ModerationManager())->getModeratorAmount($this->user->identity->id)
        ]);
    }

    public function actionView_object($pid, $id, $type)
    {
        $obj = (new ModerationManager())->findModerationObjectById($pid);

        return $this->renderPartial('objects/' . $type, [
            'object'  => (new ModerationObject([
                'foreign_table' => $obj['foreign_table'],
                'foreign_key'   => $obj['foreign_key'],
            ]))->getModeratedObject(),
            'pobject' => $obj
        ]);
    }

    /**
     * Логирует действие пользователя
     */
    public function log($description)
    {
        parent::logAction(Yii::$app->user->identity->getId(), $description);
    }

}
                                                        