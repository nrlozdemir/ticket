<?php

class ApiController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    /**
     * This action will be used for Controller:Ticket Action:index
     * Show a page in json format that contains ticket list
     *
     * @param null
     * @return json
     */
    public function indexAction()
    {
        header('Content-type:application/json; charset=utf-8');

        // get tickets list from db table
        $ticketObject = new Application_Model_Tickets();
        $ticketList = $ticketObject->listTicket();

        foreach($ticketList as $key => $ticket)
        {
            $data[$key] = $ticket;
        }

        echo json_encode($data);
    }

    /**
     * Download a csv file contains ticket list
     *
     * @param null
     * @return csv
     */
    public function csvAction()
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=tickets.csv');
        header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
        header("Content-Description: Attachment CSV Download");
        header("Content-Transfer-Encoding: binary");

        // open a pointer to php streams and set csv headers
        $output = fopen('php://output', 'w');
        fputcsv($output, array('ID', 'HEADER', 'DESCRIPTION', 'PRIORITY', 'ATTACHMENT_NAME', 'DATE'));

        // get tickets list from db table
        $ticketObject = new Application_Model_Tickets();
        $ticketList = $ticketObject->listTicket();

        // Writing all tickets to php stream
        foreach($ticketList as $key => $ticket)
        {
            fputcsv($output, $ticket);
        }
    }
}
