<?php

namespace TestTask\Model;

use TestTask\Lib\ImageUploader;

class Comment extends AbstractModel
{
    const MODERATE_NOT_CHECKED = 0;
    const MODERATE_APPROVED = 1;
    const MODERATE_REFUSED = 2;

    const SORT_DATE = 0;
    const SORT_EMAIL = 1;
    const SORT_NAME = 2;

    public $id;
    public $name;
    public $email;
    public $content;
    public $moderate = self::MODERATE_NOT_CHECKED;
    protected $image;
    public $updated_by_admin = 0;
    public $created_at;

    public static function findById($id)
    {
        $db = self::getDb();
        $stmt = $db->prepare("SELECT * FROM comment WHERE id = ?");
        $stmt->execute([$id]);
        $comments = $stmt->fetchAll(\PDO::FETCH_CLASS, self::class);
        if (!$comments) {
            return false;
        }

        return array_shift($comments);
    }

    public static function find($sort = self::SORT_DATE)
    {
        $query_sql = 'SELECT * FROM comment';
        $query_sql .= self::getOrderByFromSort($sort);

        $db = self::getDb();
        $query = $db->query($query_sql);
        return $query->fetchAll(\PDO::FETCH_CLASS, self::class);
    }

    public static function findApproved($sort = self::SORT_DATE)
    {
        $query_sql = "SELECT * FROM comment WHERE moderate = " . self::MODERATE_APPROVED;
        $query_sql .= self::getOrderByFromSort($sort);

        $db = self::getDb();
        $query = $db->query($query_sql);
        return $query->fetchAll(\PDO::FETCH_CLASS, self::class);
    }

    public function getWhiteList()
    {
        return ['name', 'email', 'content'];
    }

    private function beforeCreate()
    {
        $this->created_at = date('Y-m-d H:i:s');

        $image = ImageUploader::getUploadedImage();
        if ($image) {
            $this->image = $image;
        }
    }

    private function validate()
    {
        if (!preg_match("/^[a-zA-Z ]*$/", $this->name)) {
            $this->appendMessage('Field "name" is not valid');
            return false;
        }

        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $this->appendMessage('Field "email" is not valid');
            return false;
        }

        return true;
    }

    public function create($data = null)
    {
        if ($data) {
            $this->assign($data);
        }

        $this->beforeCreate();
        if (!$this->validate()) {
            return false;
        }

        $db = self::getDb();
        $stmt = $db->prepare('INSERT INTO comment (name, email, content, moderate, image, created_at, updated_by_admin) VALUES (?, ?, ?, ?, ?, ?, ?)');
        return $stmt->execute([
            $this->name,
            $this->email,
            $this->content,
            $this->moderate,
            $this->image,
            $this->created_at,
            $this->updated_by_admin
        ]);
    }

    public function update($data = null)
    {
        if (!Admin::isLogined()) {
            return false;
        }

        if ($data) {
            $this->assign($data);
        }

        if (!$this->validate()) {
            return false;
        }

        $db = self::getDb();
        $stmt = $db->prepare('UPDATE comment SET name = ?, email = ?, content = ?, moderate = ?, image = ?, created_at = ?, updated_by_admin = ? WHERE id = ?');
        return $stmt->execute([
            $this->name,
            $this->email,
            $this->content,
            $this->moderate,
            $this->image,
            $this->created_at,
            $this->updated_by_admin,
            $this->id
        ]);
    }

    public function approve()
    {
        $this->moderate = self::MODERATE_APPROVED;
        return $this->update();
    }

    public function refuse()
    {
        $this->moderate = self::MODERATE_REFUSED;
        return $this->update();
    }

    public function getImage()
    {
        if (!$this->image) {
            return false;
        }

        return 'uploads/' . $this->image;
    }

    public function isChecked()
    {
        return $this->moderate == self::MODERATE_NOT_CHECKED;
    }

    private static function getOrderByFromSort($sort)
    {
        $order_by = " ORDER BY ";
        if ($sort == self::SORT_DATE) {
            $order_by .= "created_at DESC";
        } elseif ($sort == self::SORT_EMAIL) {
            $order_by .= "email ASC";
        } elseif ($sort == self::SORT_NAME) {
            $order_by .= 'name ASC';
        }

        return $order_by;
    }
}
