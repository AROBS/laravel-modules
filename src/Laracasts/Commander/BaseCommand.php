<?php
/**
* This file is part of the Overpass-Ruckus project.
*
* (c) AROBS Transilvania Software http:///www.arobs.com
*
* Created by Sony at 11/4/2014 2:47 PM. Last modified by  Sony at 11/4/2014 2:47 PM
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Laracasts\Commander;


class BaseCommand {

    /**
     * @var mixed
     */
    protected $_input;



    /**
     * @return mixed
     */
    public function getInput()
    {
        return $this->_input;
    }

    /**
     * @param mixed $input
     */
    public function setInput($input)
    {
        $this->_input = $input;
    }



}