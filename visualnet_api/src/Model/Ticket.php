<?php

namespace Drupal\visualnet_api\Model;

use Moment\Moment;

/**
 * Class Ticket
 * @package Drupal\visualnet_api\Model
 */
class Ticket extends Entity
{
    protected $id;
    protected $repertoire;
    protected $barcode;
    protected $returnedAt;
    protected $enteredAt;
    protected $pass;

    /**
     * Ticket constructor.
     *
     * @param \stdClass $entity
     * @param \stdClass $content
     */
    public function __construct(\stdClass $entity, \stdClass $content)
    {
        $this->populate($entity, $content);
    }

    /**
     * @inheritdoc
     */
    protected function populate(\stdClass $entity, \stdClass $content)
    {
        parent::populate($entity, $content);

        $this->checkIfPropertiesExists($entity, [
            'barcode',
            'returnedAt',
            'enteredAt',
            'repertoire',
            'pass',
        ], __CLASS__);

        $this->checkIfPropertiesExists($content, ['repertoires'], __CLASS__);

        $this->setProperties($entity, [
            'barcode', 'returnedAt', 'enteredAt', 'pass', 'id',
        ]);

    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getBarcode()
    {
        return $this->barcode;
    }

    /**
     * @return int
     */
    public function getUnixDate()
    {
        $repertoire = new Repertoire(
            $this->find($this->content->repertoires, $this->entity->repertoire),
            $this->content
        );

        $unix = $repertoire->getUnixDate();
        unset($repertoire);

        return $unix;
    }

    /**
     * @return mixed
     */
    public function getRepertoire()
    {
        return new Repertoire(
            $this->find($this->content->repertoires, $this->entity->repertoire),
            $this->content
        );
    }

    /**
     * @return mixed
     */
    public function getReturnedAt()
    {
        return $this->returnedAt;
    }

    /**
     * @return mixed
     */
    public function getEnteredAt()
    {
        return $this->enteredAt;
    }

    /**
     * @return bool
     */
    public function hasPass()
    {
        return (!is_null($this->pass));
    }

    /**
     * @return bool
     */
    public function isBeforeEnteredAt()
    {
        $moment      = new Moment($this->enteredAt);
        $dateFromNow = $moment->fromNow();

        $diff = $dateFromNow->getSeconds();

        return ($diff < 0);
    }

    /**
     * @return bool
     */
    public function isPossibilityOfReturn()
    {
        return ($this->isBeforeEnteredAt() && !$this->returnedAt);
    }

    /**
     * @return int
     */
    public function getRepertoireYear()
    {
        $repertoire     = $this->getRepertoire();
        $repertoireDate = $repertoire->getDate();
        unset($repertoire);

        return (int) (new \DateTime($repertoireDate))->format("Y");
    }

    /**
     * @return bool
     */
    public function isPreviousDate()
    {
        $currentYear    = (new \DateTime())->format("Y");
        $repertoireYear = $this->getRepertoireYear();

        return ($currentYear > $repertoireYear);
    }

    /**
     * @return bool
     */
    public function hasCurrentYear()
    {
        $currentYear = (new \DateTime())->format("Y");
        $ticketYear  = $this->getRepertoireYear();

        return ($currentYear == $ticketYear);

    }

}
