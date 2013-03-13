<?php
require_once 'SquareMatrix.php';
/**
 * Class Binary Matrix
 * 
 * This class is specialized in creation of Binary Square Matrix objects that have it's particular properties and methods.
 * 
 * @author José Lorente Martín
 * @version 1.0
 * @copyright Copyright (c) 2012, José Lorente Martín
 */
class BinaryMatrix extends SquareMatrix
{
	protected $vector;
	
	/**
	 * 
	 * Enter description here ...
	 * @param int $n
	 * @param array $_binaryMatrix
	 * @throws Exception
	 */
	public function __construct($n, $_binaryMatrix = null)
	{
		if (!is_int($n)) {
			throw new Exception('Invalid param - $n must be integer');
		}
		
		parent::__construct($n, $_binaryMatrix);
		
		for ($i = 0; $i < $this->getRowCount(); ++$i) {
			$compareArray = array_diff($this->getRow($i)->getData(),array(0,1));
			if (count($compareArray) > 0) {
				throw new Exception('Invalid param - Binary Matrix must only content 0\'s and 1\'s');
			}
		}
	}
		
	/**
	 * 
	 * Enter description here ...
	 * @return Matrix
	 */
	public function getVector()
	{
		if ($this->vector === null) {
			$this->vector = new Matrix(1, $this->getRowCount());
			for ($i = 0; $i < $this->getRowCount(); ++$i) {
				$this->vector->setElement(0, $i, array_search(1, $this->getRow($i)->getData()));
			}
		}
		return $this->vector;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see SquareMatrix::resetProperties()
	 */
	protected function resetProperties()
	{
		$this->vector = null;
		parent::resetProperties();
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param array $_vector
	 * @throws Exception
	 * @return BinaryMatrix
	 */
	public static function createFromVector($_vector)
	{
		if (!is_array($_vector)) {
			throw new Exception('Invalid param - $vector must be one dimension array');
		}

		$binaryMatrix = new BinaryMatrix(count($_vector));
		for ($i = 0; $i < $binaryMatrix->getRowCount(); ++$i) {
			if (!is_int($_vector[$i])) {
				throw new Exception("Invalid param - \$vector[$i] must be integer");
			}
			$binaryMatrix->setElement($i, $_vector[$i], 1);
		}
		return $binaryMatrix;
	}
}