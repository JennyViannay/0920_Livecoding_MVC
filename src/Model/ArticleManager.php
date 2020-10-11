<?php

namespace App\Model;

/**
 *
 */
class ArticleManager extends AbstractManager
{
    /**
     *
     */
    const TABLE = 'article';

    /**
     *  Initializes this class.
     */
    public function __construct()
    {
        parent::__construct(self::TABLE);
    }

    public function searchArticles(string $term): array
    {
        $statement = $this->pdo->prepare("SELECT * FROM " . self::TABLE . " WHERE name LIKE :search ORDER BY name ASC");
        $statement->bindValue('search', $term.'%', \PDO::PARAM_STR);
        $statement->execute();
        $articles = $statement->fetchAll();
        return $articles;
    }

    /**
     * @param array $article
     * @return int
     */
    public function insert(array $article): int
    {
        // prepared request
        $statement = $this->pdo->prepare("INSERT INTO " . self::TABLE . " (name, price, img) VALUES (:name, :price, :img)");
        $statement->bindValue('name', $article['name'], \PDO::PARAM_STR);
        $statement->bindValue('price', $article['price'], \PDO::PARAM_INT);
        $statement->bindValue('img', $article['img'], \PDO::PARAM_STR);

        if ($statement->execute()) {
            return (int)$this->pdo->lastInsertId();
        }
    }


    /**
     * @param int $id
     */
    public function delete(int $id): void
    {
        // prepared request
        $statement = $this->pdo->prepare("DELETE FROM " . self::TABLE . " WHERE id=:id");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();
    }


    /**
     * @param array $article
     * @return bool
     */
    public function update(array $article):bool
    {

        // prepared request
        $statement = $this->pdo->prepare("UPDATE " . self::TABLE . " SET name=:name, price=:price, img=:img WHERE id=:id");
        $statement->bindValue('id', $article['id'], \PDO::PARAM_INT);
        $statement->bindValue('name', $article['name'], \PDO::PARAM_STR);
        $statement->bindValue('price', $article['price'], \PDO::PARAM_INT);
        $statement->bindValue('img', $article['img'], \PDO::PARAM_STR);

        return $statement->execute();
    }
}
