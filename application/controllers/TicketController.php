<?php

class TicketController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    /**
     * @Route("/ticket", name="indexAction")
     *
     * @param null
     *
     * @return template
     */
    public function indexAction()
    {
        // create api url with http protocol
        $apiRoute = $this->view->url(array('controller' => 'api', 'action' => 'index'), 'default', true);
        $buildApiUrl = $this->view->serverUrl() . $apiRoute;

        // make request to api for getting all tickets, we will got response from our api server
        $client = new Zend_Http_Client();
        $client->setUri($buildApiUrl);
        $request = $client->request('GET');

        // object to array type
        $json = json_decode($request->getBody(), true);

        // assign to view for using in foreach loop
        $this->view->ticketsData = $json;
    }

    /**
     * @Route("/ticket/add", name="addAction")
     *
     * @param Post $post
     *
     * @return template
     */
    public function addAction()
    {
        if($this->getRequest()->isPost())
        {
            /*
             * Call the required validation objects
             */
            $notEmpty = new Zend_Validate_NotEmpty();
            $between = new Zend_Validate_Between(array('min' => 1, 'max' => 5));
            $stringLength = new Zend_Validate_StringLength();
            $fileUpload = new Zend_Validate_File_Upload();

            $errors = array();
            $inserted = 0;

            /*
             * @var string
             * @required
             * @length(255)
             * @text('label' => 'Header')
             */
            $header = $this->getRequest()->getPost('header', null);

            // Validate $header
            $stringLength->setMax(255);
            $stringLength->setMin(1);
            $stringLength->setEncoding('utf-8');
            if( ! $notEmpty->isValid($header) OR ! ($stringLength->isValid($header)))
            {
                $errors[] = 'Check the `header` field can get min:1 and max:255 characters.';
            }

            /*
             * @var string
             * @required
             * @length(400)
             * @text('label' => 'Description')
             */
            $description = $this->getRequest()->getPost('description', null);

            // Validate $description
            $stringLength->setMax(400);
            $stringLength->setMin(1);
            $stringLength->setEncoding('utf-8');
            if( ! $notEmpty->isValid($description) OR ! ($stringLength->isValid($description)))
            {
                $errors[] = 'Check the `description` field can get min:1 and max:400 characters.';
            }

            /*
             * @var integer
             * @required
             * @var int
             * @range(1, 5)
             */
            $priority = (int)$this->getRequest()->getPost('priority', null);

            // Validate $priority
            if( ! $between->isValid($priority))
            {
                $errors[] = 'Check the `description` field can get (only) 1 to 5.';
            }

            /*
             * @var string
             * @required
             * @length(255)
             * @text('label' => 'Attachment')
             */
            $attachmentName = $_FILES['attachment']['name'];

            /*
             * @var blob
             * Referenced from $attachmentName
             */
            $attachmentFile = $_FILES['attachment']['tmp_name'];

            // Validate @attachment
            if( ! $fileUpload->isValid('attachment', $_FILES['attachment']['tmp_name']))
            {
                $errors[] = 'Check the `file` field.';
            }

            /*
             * @var integer
             */
            $date = time();

            if(count($errors) == 0)
            {
                try
                {
                    // Call the Tickets Model and send post data to `addTicket` function
                    $ticketObject = new Application_Model_Tickets();
                    $result = $ticketObject->addTicket(array(
                        'header' => $header,
                        'description' => $description,
                        'priority' => $priority,
                        'attachment_name' => $attachmentName,
                        'attachment_content' => $attachmentFile,
                        'date' => $date
                    ));

                    /* assign $result['message'] that will be returned from addTicket function */
                    $this->view->templateMessage = $result['message'];
                    if((int)$result['insert_id'] > 0)
                    {
                        $inserted = 1;
                    }
                }
                catch(Exception $e)
                {
                    // Right here we have a lot of problems, so application going to die.
                    echo 'An error occured, something went wrong!';
                    exit;
                }

                // assign $inserted variable to view for controlling messages
                $this->view->inserted = $inserted;
            }
            else
            {
                // assign $errors array to managing form errors
                $this->view->errorMessageStr = implode('<br>', $errors);
            }
        }
    }

    /**
     * @Route("/ticket/download/id", name="downloadAction", requirements={"id": "\d+"})
     *
     * @param null
     *
     * @return null
     */
    public function downloadAction()
    {
        $fileId = $this->getRequest()->getParam('id');

        $ticketObject = new Application_Model_Tickets();

        $ticketObject->downloadFile((int)$fileId);
    }
}