<?php

namespace TestTask\Controller;

use TestTask\Lib\Application;
use TestTask\Lib\Security;
use TestTask\Lib\View;
use TestTask\Model\Admin;
use TestTask\Model\Comment;

class CommentController
{
    public function indexAction()
    {
        $sort = null;
        if (isset($_GET['sort'])) {
            $sort = $_GET['sort'];
        }

        $message = null;
        $new_comment = new Comment();
        if ($_POST && $_POST['token']) {
            if (Security::checkToken($_POST['token'])) {
                if ($new_comment->create($_POST)) {
                    $message = 'Successful created';
                    $new_comment = new Comment();
                } else {
                    $message = $new_comment->getMessage();
                }
            } else {
                $message = 'Wrong token';
            }
        }

        if (Admin::isLogined()) {
            $comments = Comment::find($sort);
        } else {
            $comments = Comment::findApproved($sort);
        }

        $token = Security::generateToken();

        View::render('comment/index.phtml', [
            'new_comment' => $new_comment,
            'comments' => $comments,
            'message' => $message,
            'token' => $token
        ]);
    }

    public function approveAction()
    {
        $comment = $this->getComment();
        if (!$comment) {
            return;
        }

        $comment->approve();
        Application::redirect();
    }

    public function refuseAction()
    {
        $comment = $this->getComment();
        if (!$comment) {
            return;
        }

        $comment->refuse();
        Application::redirect();
    }

    public function editAction()
    {
        $comment = $this->getComment();
        if (!$comment) {
            return;
        }

        $message = null;

        if ($_POST && $_POST['token']) {
            if (Security::checkToken($_POST['token'])) {
                $comment->updated_by_admin = 1;
                if ($comment->update($_POST)) {
                    $message = 'Successful updated';
                } else {
                    $message = $comment->getMessage();
                }
            } else {
                $message = 'Wrong token';
            }
        }

        $token = Security::generateToken();

        View::render('comment/edit.phtml', [
            'comment' => $comment,
            'message' => $message,
            'token' => $token
        ]);
    }

    private function getComment()
    {
        if (!Admin::isLogined()) {
            call_user_func([new ExceptionController(), 'accessDeniedAction']);
            return false;
        }

        if (empty($_GET['id'])) {
            call_user_func([new ExceptionController(), 'notFoundAction']);
            return false;
        }

        $id = $_GET['id'];
        $comment = Comment::findById($id);
        if (!$comment) {
            call_user_func([new ExceptionController(), 'notFoundAction']);
            return false;
        }

        return $comment;
    }
}
