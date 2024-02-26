<?php

namespace App\Class;

use App\Controller\AbstractController;
use App\Router\Router;

class Controller extends AbstractController
{



    public function admin($action = 'list', $entity = 'user', $id = null)
    {
        if (self::getUser() === null || !in_array('ROLE_ADMIN', self::getUser()->getRole())) {
            $this->redirect('home');

            return;
        }

        $action = $action . 'Admin';

        if (method_exists($this, $action)) {
            $this->$action($entity, $id);
        } else {
            throw new \Exception("L'action demandée n'existe pas");
        }
    }

    public function listAdmin($entity)
    {
        $entity = ucfirst($entity);
        $className = "App\\Class\\$entity";
        $class = new $className();
        $entities = $class->findAll();
        $entities = array_map(function ($instance) {
            return $instance->toArray();
        }, $entities);
        $this->render('admin/list', [
            'entities' => $entities,
            'entityName' => $entity
        ]);
    }

    public function showAdmin($entity, $id)
    {
        $entity = ucfirst($entity);
        $className = "App\\Class\\$entity";
        $class = new $className();
        $instance = $class->findOneById($id);
        $this->render('admin/show', [
            'entity' => $instance,
            'entityName' => $entity
        ]);
    }

    public function editAdmin($entity, $id)
    {
        $entity = ucfirst($entity);
        $className = "App\\Class\\$entity";
        $class = new $className();
        $instance = $class->findOneById($id);
        foreach ($_POST as $key => $value) {
            $key = explode('_', $key);
            $key = array_map(function ($word) {
                return ucfirst($word);
            }, $key);
            $key = implode('', $key);
            $value = htmlspecialchars($value);
            $getter = 'get' . $key;
            if (method_exists($instance, $getter) && is_array($instance->$getter())) {
                $value = explode(', ', $value);
            }
            if (is_string($value) && strtotime($value)) {
                $value = (new \DateTime())->setTimestamp(strtotime($value));
            }
            if ($key === 'User' || $key === 'Post' || $key === 'Comments' || $key === 'Category') {
                continue;
            }
            $setter = 'set' . $key;
            var_dump([$key, $value]);
            if (method_exists($instance, $setter) && null !== $instance->$getter()) {
                $instance->$setter($value);
            }
        }

        $instance->save();

        $this->redirect('admin', ['entity' => strtolower($entity), 'action' => 'list']);
    }

    public function deleteAdmin($entity, $id)
    {
        $entity = ucfirst($entity);
        $className = "App\\Class\\$entity";
        $class = new $className();
        $instance = $class->findOneById($id);
        $instance->delete();

        if ($entity === 'User' && $id === self::getUser()->getId()) {
            $this->redirect('logout');
        }

        $this->redirect('admin', ['entity' => strtolower($entity), 'action' => 'list']);
    }
}
