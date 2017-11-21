<?php

namespace Application\ViewHelper;

use Zend\View\Helper\AbstractHelper;
use Application\Service;

/*********************************************************************************************************************\
 * LAST UPDATE
 * ============
 * March 22, 2007
 *
 *
 * AUTHOR
 * =============
 * Kwaku Otchere 
 * ospinto@hotmail.com
 * 
 * Thanks to Andrew Hewitt (rudebwoy@hotmail.com) for the idea and suggestion
 * 
 * All the credit goes to ColdFusion's brilliant cfdump tag
 * Hope the next version of PHP can implement this or have something similar
 * I love PHP, but var_dump BLOWS!!!
 *
 * FOR DOCUMENTATION AND MORE EXAMPLES: VISIT http://dbug.ospinto.com
 *
 *
 * PURPOSE
 * =============
 * Dumps/Displays the contents of a variable in a colored tabular format
 * Based on the idea, javascript and css code of Macromedia's ColdFusion cfdump tag
 * A much better presentation of a variable's contents than PHP's var_dump and print_r functions
 *
 *
 * USAGE
 * =============
 * new dBug ( variable [,forceType] );
 * example:
 * new dBug ( $myVariable );
 *
 * 
 * if the optional "forceType" string is given, the variable supplied to the 
 * function is forced to have that forceType type. 
 * example: new dBug( $myVariable , "array" );
 * will force $myVariable to be treated and dumped as an array type, 
 * even though it might originally have been a string type, etc.
 *
 * NOTE!
 * ==============
 * forceType is REQUIRED for dumping an xml string or xml file
 * new dBug ( $strXml, "xml" );
 * 
\*********************************************************************************************************************/

class DBugHelper extends AbstractHelper {


    /**
     * __invoke
     *
     * @access public
     * @param  array|xml|object $var
     * @param  string $forceType array|xml|object
     * @param  object $bCollapsed
     * @return String
     */
    public function __invoke($var,$forceType="",$bCollapsed=false) {
        new Service\DBug( $var, $forceType, $bCollapsed);
    }

}