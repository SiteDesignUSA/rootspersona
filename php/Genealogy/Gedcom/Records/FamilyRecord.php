<?php
/**
 * \Genealogy\Gedcom\Records\FamilyRecord
 *
 * PHP version 5
 *
 * @category  File_Formats
 * @package   Genealogy_Gedcom_Records
 * @author    Ed Thompson <ed4becky@gmail.com>
 * @copyright 2010 Ed Thompson
 * @license   http://www.opensource.org/licenses/Apache2.0.php Apache License
 * @version   SVN: $Id: FamilyRecord.php 306 2010-04-13 22:16:26Z ed4becky $
 * @link      http://svn.php.net/repository/Genealogy_Gedcom
 */





/**
 * FamilyRecord class for Genealogy_Gedcom
 *
 * @category  File_Formats
 * @package   Genealogy_Gedcom_Records
 * @author    Ed Thompson <ed4becky@gmail.com>
 * @copyright 2010 Ed Thompson
 * @license   http://www.opensource.org/licenses/Apache2.0.php Apache License
 * @link      http://svn.php.net/repository/Genealogy_Gedcom
 */
class FamilyRecord extends EntityAbstract
{
    /**
     *
     * @var string
     */
    var $Id;
    /**
     *
     * @var string
     */
    var $Restriction;
    /**
     *
     * @var array
     */
    var $Events= array();
    /**
     *
     * @var string
     */
    var $Husband;
    /**
     *
     * @var string
     */
    var $Wife;
    /**
     *
     * @var array
     */
    var $Children = array();
    /**
     *
     * @var int
     */
    var $CountOfChildren;
    /**
     *
     * @var array
     */
    var $LdsSealings = array();
    /**
     *
     * @var array
     */
    var $UserRefNbrs = array();
    /**
     *
     * @var array
     */
    var $SubmitterLinks = array();
    /**
     *
     * @var string
     */
    var $AutoRecId;
    /**
     *
     * @var string
     */
    var $ChangeDate;
    /**
     *
     * @var array
     */
    var $Notes = array();
    /**
     *
     * @var array
     */
    var $Citations = array();
    /**
     *
     * @var array
     */
    var $MediaLinks = array();

    /**
     * Initializes complex attributes
     *
     * @return none
     */
    public function __construct()
    {
        $this->ChangeDate = new ChangeDate();
    }

    /**
     * Returns the nth instance of a specific event type
     *
     * @param string $tag    event type of interest
     * @param int    $offset instance nbr
     */
    public function getEvent($tag, $offset=1) {
        $events = $this->Events;
        $idx = 1;
        foreach($events as $event) {
            if($event->Tag === $tag
            || ($event->Tag === 'EVEN'
            && $event->Type === $tag)) {
                if($offset == $idx) {
                    return $event;
                }
                $idx++;
            }
        }
        return false;
    }
    /**
     * Flattens the object into a GEDCOM compliant format
     *
     * This method guarantees compliance, not re-creation of
     * the original order of the records.
     *
     * @param int    $lvl indicates the level at which this record
     *                    should be generated
     * @param string $ver represents the version of the GEDCOM standard
     *
     * @return string a return character delimited string of gedcom records
     *
     * @access public
     * @since Method available since Release 0.0.1
     */
    public function toGedcom($lvl, $ver)
    {
        $gedRec = '';
        if (!isset($ver) || $ver === '') {
            $ver = $this->Ver;
        }

        if (strpos($ver, '5.5.1') == 0) {
            $gedRec = $lvl . ' @' .$this->Id. '@ ' . rpTags::FAMILY;
            $lvl2 = $lvl + 1;
            if (isset($this->Restriction) && $this->Restriction != '') {
                $gedRec .= "\n" . $lvl2 . ' '
                    . rpTags::RESTRICTION .' ' .$this->Restriction;
            }
            if (isset($this->Husband) && $this->Husband != '') {
                $gedRec .= "\n" . $lvl2 . ' '
                    . rpTags::HUSBAND .' @' .$this->Husband . '@';
            }
            if (isset($this->Wife) && $this->Wife != '') {
                $gedRec .= "\n" . $lvl2
                    . ' ' . rpTags::WIFE .' @' .$this->Wife . '@';
            }
            for ($i=0; $i<count($this->Children); $i++) {
                $gedRec .= "\n" . $lvl2
                    . ' ' . rpTags::CHILD .' @' .$this->Children[$i] . '@';
            }
            if (isset($this->CountOfChildren) && $this->CountOfChildren != '') {
                $gedRec .= "\n" . $lvl2
                    . ' ' . rpTags::CHILDCNT .' ' .$this->CountOfChildren;
            }
            for ($i=0; $i<count($this->Events); $i++) {
                $gedRec .= "\n" . $this->Events[$i]->toGedcom($lvl2, $ver);
            }
            //			for ($i=0; $i<count($this->LdsSealings); $i++) {
            //				$gedRec .= "\n" . $this->LdsSealings[$i]->toGedcom($lvl2, $ver);
            //			}
            for ($i=0; $i<count($this->SubmitterLinks); $i++) {
                $gedRec .= "\n" . $lvl2
                    . ' ' . rpTags::SUBMITTER . ' @' . $this->SubmitterLinks[$i] . '@';
            }
            for ($i=0; $i<count($this->UserRefNbrs); $i++) {
                $gedRec .= "\n" . $lvl2
                    . ' ' . rpTags::USERFILE . ' ' . $this->UserRefNbrs[$i]['Nbr'];
                if (isset($this->UserRefNbrs[$i]['Type'])) {
                    $gedRec .= "\n" .($lvl2+1)
                        . ' ' . rpTags::TYPE . ' ' . $this->UserRefNbrs[$i]['Type'];
                }
            }
            if (isset($this->AutoRecId) && $this->AutoRecId != '') {
                $gedRec .= "\n" . $lvl2
                    . ' ' . rpTags::AUTORECID .' ' .$this->AutoRecId;
            }
            $tmp = $this->ChangeDate->toGedcom($lvl2, $ver);
            if (isset($tmp) && $tmp != '') {
                $gedRec .= "\n" . $tmp;
            }
            for ($i=0; $i<count($this->Citations); $i++) {
                $gedRec .= "\n" . $this->Citations[$i]->toGedcom($lvl2, $ver);
            }
            for ($i=0; $i<count($this->MediaLinks); $i++) {
                $gedRec .= "\n" . $this->MediaLinks[$i]->toGedcom($lvl2, $ver);
            }
            for ($i=0; $i<count($this->Notes); $i++) {
                $gedRec .= "\n" . $this->Notes[$i]->toGedcom($lvl2, $ver);
            }

        }
        return $gedRec;
    }

    /**
     * Extracts attribute contents from a parent tree object
     *
     * @param array  $tree an array containing an array from which the
     *                     object data should be extracted
     * @param string $ver  represents the version of the GEDCOM standard
     *                     data is being extracted from
     *
     * @return void
     *
     * @access public
     * @since Method available since Release 0.0.1
     */
    public function parseTree($tree, $ver)
    {
        $this->Id = parent::parseRefId($tree[0], rpTags::FAMILY);
        if (isset($tree[0][1])) {
            $sub2 = $tree[0][1];
            if (($i1=parent::findTag($sub2, rpTags::RESTRICTION))!==false) {
                $this->Restriction
                    = parent::parseText($sub2 [$i1], rpTags::RESTRICTION);
            }
            $tmp = new rpEvent();
            $this->Events = $tmp->parseTreeToArray($sub2, $ver);

            if (($i1=parent::findTag($sub2, rpTags::HUSBAND))!==false) {
                $this->Husband = parent::parsePtrId($sub2 [$i1], rpTags::HUSBAND);
            }
            if (($i1=parent::findTag($sub2, rpTags::WIFE))!==false) {
                $this->Wife = parent::parsePtrId($sub2 [$i1], rpTags::WIFE);
            }
            $off = 0;
            while (($i1=parent::findTag($sub2, rpTags::CHILD, $off))!==false) {
                $this->Children[]
                    = parent::parsePtrId($sub2 [$i1], rpTags::CHILD);
                $off = $i1 + 1;
            }
            if (($i1=parent::findTag($sub2, rpTags::CHILDCNT))!==false) {
                $this->CountOfChildren
                    = parent::parseText($sub2 [$i1], rpTags::CHILDCNT);
            }
            // TODO add support for LdsSealing
            //			$tmp = new LdsSealing();
            //			$this->LdsSealings = $tmp->parseTreeToArray($sub2, $ver);
            $off = 0;
            while (($i1=parent::findTag($sub2, rpTags::SUBMITTER, $off))!==false) {
                $this->SubmitterLinks[]
                    = parent::parsePtrId($sub2 [$i1], rpTags::SUBMITTER);
                $off = $i1 + 1;
            }
            if (($i1=parent::findTag($sub2, rpTags::USERFILE))!==false) {
                $this->UserRefNbrs[]['Nbr']
                    = parent::parseText($sub2 [$i1], rpTags::USERFILE);
                if (isset($sub2[$i1][1])) {
                    if (($i2=parent::findTag($sub2[$i1][1], rpTags::TYPE)) !== false) {
                        $this->UserRefNbrs[count($this->UserRefNbrs)-1]['Type']
                            = parent::parseText($sub2 [$i1][1][$i2], rpTags::TYPE);
                    }
                }
            }
            if (($i1=parent::findTag($sub2, rpTags::AUTORECID))!==false) {
                $this->AutoRecId = parent::parseText($sub2 [$i1], rpTags::AUTORECID);
            }

            if (($i1=parent::findTag($sub2, rpTags::CHANGEDATE))!==false) {
                $this->ChangeDate->parseTree(array($sub2[$i1]), $ver);
            }

            $off = 0;
            while (($i1=parent::findTag($sub2, rpTags::CITE, $off))!==false) {
                $tmp = new Citation();
                $tmp->parseTree(array($sub2[$i1]), $ver);
                $this->Citations[] = $tmp;
                $off = $i1 + 1;
            }
            $off = 0;
            while (($i1=parent::findTag($sub2, rpTags::MEDIA, $off))!==false) {
                $tmp = new MediaLink();
                $tmp->parseTree(array($sub2[$i1]), $ver);
                $this->MediaLinks[] = $tmp;
                $off = $i1 + 1;
            }
            $off = 0;
            while (($i1=parent::findTag($sub2, rpTags::NOTE, $off))!==false) {
                $tmp = new Note();
                $tmp->parseTree(array($sub2[$i1]), $ver);
                $this->Notes[] = $tmp;
                $off = $i1 + 1;
            }
        }
    }

    /**
     * Creates a string representation of the object
     *
     * @return string  contains string representation of each
     *                 public field in the object
     *
     * @access public
     * @since Method available since Release 0.0.1
     */
    public function __toString()
    {
        $str = __CLASS__
        . '(Id->' . $this->Id
        . ', Restriction->' . $this->Restriction
        . ', Events->(';

        for ($i=0; $i<count($this->Events); $i++) {
            $str .= "\n" . $this->Events[$i];
        }
        $str .= '), Husband->' . $this->Husband
        . ', Wife->' . $this->Wife;

        for ($i=0; $i<count($this->Children); $i++) {
            $str .= ", Child->" . $this->Children[$i];
        }

        $str .= ', CountOfChildren->' . $this->CountOfChildren;
        $str .= ', LdsSealings->(';
        for ($i=0; $i<count($this->LdsSealings); $i++) {
            $str .= "\n" . $this->LdsSealings[$i];
        }
        $str .= '), UserRefNbrs->(';
        for ($i=0; $i<count($this->UserRefNbrs); $i++) {
            $str .= "UserRefNbr->" . $this->UserRefNbrs[$i]['Nbr']
                . ' (' . $this->UserRefNbrs[$i]['Type'] . ')';
        }
        $str .= '), AutoRecId->' . $this->AutoRecId
        . ', ChangeDate->' . $this->ChangeDate
        . ', SubmitterLinks->(';
        for ($i=0; $i<count($this->SubmitterLinks); $i++) {
            if ($i > 0) {
                $str .= ', ';
            }
            $str .= "Submitter->" . $this->SubmitterLinks[$i];
        }
        $str .= '), Citations->(';
        for ($i=0; $i<count($this->Citations); $i++) {
            $str .= "\n" . $this->Citations[$i];
        }
        $str .= '), MediaLinks->(';
        for ($i=0; $i<count($this->MediaLinks); $i++) {
            $str .= "\n" . $this->MediaLinks[$i];
        }
        $str .= '), Notes->(';
        for ($i=0; $i<count($this->Notes); $i++) {
            $str .= "\n" . $this->Notes[$i];
        }
        $str .= '))';
        return $str;
    }
}
?>