<?php

class Application_Model_Tickets extends Application_Model_Pdo
{
    /**
     * Reference Application_Model_Pdo in models/Pdo.php
     * @var object
     */
    private $pdo = null;

    function __construct()
    {
        $pdoObject = new Application_Model_Pdo();
        $this->pdo = $pdoObject->connection;
    }

    /**
     * This function is using for adding ticket to db table
     *
     * @param array
     * @return array
     */
    public function addTicket($array)
    {
        $sql = "INSERT INTO tickets(header, description, priority, attachment_name, attachment_content, date) 
                            VALUES(:header,:description,:priority,:attachment_name,:attachment_content,:date)";
        $query = $this->pdo->prepare($sql);

        $query->bindParam(':header', $array['header']);
        $query->bindParam(':description', $array['description']);
        $query->bindParam(':priority', $array['priority']);
        $query->bindParam(':attachment_name', $array['attachment_name']);
        $query->bindParam(':attachment_content', fopen($array['attachment_content'], 'rb'), PDO::PARAM_LOB);
        $query->bindParam(':date', $array['date']);

        $insertId = null;
        if($query->execute())
        {
            $insertId = $this->pdo->lastInsertId();
            $message = 'This data successfully inserted.';
        }
        else
        {
            $message = 'This data can\'t inserted, please check the fields.';
        }

        return array('message' => $message, 'insert_id' => $insertId);
    }

    /**
     * This function is using for listing tickets existed in db table
     * Affecting Controller:Api Action:[index, csv]
     *
     * @param null
     * @return array
     */
    public function listTicket()
    {
        $query = $this->pdo->query("SELECT `id`, `header`, `description`, `priority`, `attachment_name`, `date`
                                    FROM `tickets`
                                    ORDER BY `date` DESC"
                                    , PDO::FETCH_ASSOC);
        $result = array();
        if($query->rowCount())
        {
            foreach($query as $row)
            {
                $result[] = $row;
            }
        }

        return $result;
    }

    /**
     * This function is using for general attachment download
     *
     * @param integer
     * @return null
     */
    public function downloadFile($fileId)
    {
        $query = $this->pdo->query("SELECT `attachment_name`, `attachment_content` 
                                    FROM `tickets` 
                                    WHERE `id` = $fileId");
        $query->execute();
        $result = $query->fetch();

        $attachmentName = $result['attachment_name'];
        $attachmentContent = $result['attachment_content'];

        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment; filename=$attachmentName");
        header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
        header("Content-Description: Attachment Download");
        header("Content-Transfer-Encoding: binary");

        echo $attachmentContent;
    }
}