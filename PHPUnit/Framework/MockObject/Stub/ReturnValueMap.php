<?php
/**
 * PHPUnit
 *
 * Copyright (c) 2010-2011, Sebastian Bergmann <sb@sebastian-bergmann.de>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Sebastian Bergmann nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package    PHPUnit_MockObject
 * @author     Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @copyright  2010-2011 Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link       http://github.com/sebastianbergmann/phpunit-mock-objects
 * @since      File available since Release 3.6.0
 */

/**
 *
 *
 * @package    PHPUnit_MockObject
 * @author     Giorgio Sironi <piccoloprincipeazzurro@gmail.com>
 * @copyright  2010-2011 Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://github.com/sebastianbergmann/phpunit-mock-objects
 * @since      Class available since Release 3.6.0
 */
class PHPUnit_Framework_MockObject_Stub_ReturnValueMap implements PHPUnit_Framework_MockObject_Stub
{
    protected $valueMap;
    protected $valueSpecifications = array();

    public function __construct($valueMap = array())
    {
        $this->valueMap = $valueMap;
    }

    public function invoke(PHPUnit_Framework_MockObject_Invocation $invocation)
    {
        $value = $this->valueMap;
        foreach ($this->valueSpecifications as $spec) {
            if ($spec->matches($invocation->parameters)) {
                return $spec->value();
            }
        }
        foreach ($invocation->parameters as $param) {
            $value = $value[$param];
        }
        return $value;
    }

    /**
     * Accepts the same signature of the mocked method. Adds the parameter
     * to the matching list.
     * @return PHPUnit_Framework_MockObject_Stub_ReturnValueMap_ValueSpec
     */
    public function record()
    {
        $arguments = func_get_args();
        $spec = new PHPUnit_Framework_MockObject_Stub_ReturnValueMap_ValueSpec($this, $arguments);
        $this->valueSpecifications[] = $spec;
        return $spec;
    }

    public function toString()
    {
        return "return a value extracted from the map:\n" . print_r($this->valueMap, true);
    }
}

class PHPUnit_Framework_MockObject_Stub_ReturnValueMap_ValueSpec
{
    /**
     * @PHPUnit_Framework_MockObject_Stub_ReturnValueMap
     */
    protected $map;

    /**
     * @var array   elements are PHPUnit_Framework_Constraint
     */
    protected $arguments;

    protected $returnValue;

    public function __construct(PHPUnit_Framework_MockObject_Stub_ReturnValueMap $map, array $arguments)
    {
        $this->map = $map;
        foreach ($arguments as $argument) {
            if ($argument instanceof PHPUnit_Framework_Constraint) {
                $this->arguments[] = $argument;
            } else {
                $this->arguments[] = new PHPUnit_Framework_Constraint_IsEqual($argument);
            }
        }
    }

    /**
     * @param mixed $returnValue
     * @return PHPUnit_Framework_MockObject_Stub_ReturnValueMap
     */
    public function returns($returnValue)
    {
        $this->returnValue = $returnValue;
        return $this->map;
    }

    /**
     * @internal
     */
    public function matches($arguments)
    {
        $satisfied = true;
        foreach ($this->arguments as $index => $expectedArgument) {
            if (!$expectedArgument->evaluate($arguments[$index])) {
                $satisfied = false;
            }
        }
        return $satisfied;
    }

    /**
     * @internal
     */
    public function value()
    {
        return $this->returnValue;
    }
}
