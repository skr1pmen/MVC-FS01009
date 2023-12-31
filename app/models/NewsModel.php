<?php

namespace app\models;

use app\core\BaseModel;

class NewsModel extends BaseModel
{
    public function add($news_data)
    {
        $result = false;
        $error_message = '';

        if (empty($news_data['title'])) {
            $error_message .= "Введите наименование!<br>";
        }
        if (empty($news_data['short_description'])) {
            $error_message .= "Введите краткое описание!<br>";
        }
        if (empty($news_data['description'])) {
            $error_message .= "Введите описание!<br>";
        }

        if (empty($error_message)) {
            $result = $this->insert(
                "INSERT INTO news (title, short_description, description, date_create, user_id)
                VALUES (:title, :short_description, :description, NOW(), :user_id)",
                [
                    "title" => $news_data['title'],
                    "short_description" => $news_data['short_description'],
                    "description" => $news_data['description'],
                    "user_id" => $_SESSION['user']['id']
                ]
            );
        }

        return [
          'result' => $result,
          'error_message' => $error_message
        ];
    }

    public function getListNews()
    {
        $result = null;

        $news = $this->select("SELECT * FROM news");
        if (!empty($news)) {
            $result = $news;
        }
        return $result;
    }

    public function getNewsById($id)
    {
        $result = null;

        $news = $this->select("SELECT * FROM news WHERE id = :id", [
            'id' => $id
        ]);
        if (!empty($news[0])) {
            $result = $news[0];
        }
        return $result;
    }

    public function edit($id, $news_data)
    {
        $result = false;
        $error_message = "";

        if (empty($id)) {
            $error_message .= "Отсутствует id записи!<br>";
        }
        if (empty($news_data['title'])) {
            $error_message .= "Введите заголовок новости!<br>";
        }
        if (empty($news_data['short_description'])) {
            $error_message .= "Введите краткое описание!<br>";
        }
        if (empty($news_data['description'])) {
            $error_message .= "Введите описание!<br>";
        }

        if (empty($error_message)) {
            $result = $this->update(
                "UPDATE news SET title = :title, short_description = :short_description, description = :description
                WHERE id = :id",
                [
                    'title' => $news_data['title'],
                    'short_description' => $news_data['short_description'],
                    'description' => $news_data['description'],
                    'id' => $id
                ]
            );
        }

        return [
            'result' => $result,
            'error_message' => $error_message
        ];
    }

    public function deleteById($id)
    {
        $result = false;
        $error_message = '';

        if (empty($id)) {
            $error_message .= "Отсутствует id записи!<br>";
        }
        if (empty($error_message)) {
            $result = $this->delete("DELETE FROM news WHERE id = :id",['id' => $id]);
        }

        return [
            'result' => $result,
            'error_message' => $error_message
        ];
    }
}
