<?php
require_once 'Matrix.php';
/**
 * Class Square Matrix
 * 
 * This class is specialized for create Square Matrix objects that have it's particular properties and methods.
 * 
 * @author José Lorente Martín
 * @version 1.0
 * @copyright Copyright (c) 2012, José Lorente Martín
 */
class SquareMatrix extends Matrix
{
	/**
	 * Variable to store the SquareMatrix determinant.
	 * @var float
	 */
	protected $determinant;
	
	/**
	 * Variable to store the SquareMatrix trace.
	 * @var float
	 */
	protected $trace;
	
	/**
	 * Variable to store the SquareMatrix factorial value.
	 * @var int
	 */
	protected $factorialN;
	
	/**
	 * Variable to store the Identity Matrix of the SquareMatrix.
	 * @var SquareMatrix
	 */
	protected $identity;
	
	/**
	 * Creates a Square Matrix object with n rows and columns.
	 * If $_arrayMatrix is given, fills the Matrix object with it.
	 * @param int $n
	 * @param array $_arrayMatrix
	 * @throws Exception
	 * @return void
	 */
	public function __construct($n, $_arrayMatrix = null)
	{
		if (!is_int($n)) {
			throw new Exception('Invalid param - $n must be integer');
		}
		
		parent::__construct($n, $n, $_arrayMatrix);
	}
	
	/**
	 * Gets the Square Matrix factorial value
	 * @return int
	 */
	public function getFactorialN()
	{
		if ($this->factorialN === null) {
			$this->factorialN = 1;
			for ($i = 1; $i <= $this->getRowCount(); ++$i) {
				$this->factorialN *= $i;
			}
		}
		return $this->factorialN;
	}

	/**
	 * Gets the Square Matrix trace
	 * @return float
	 */
	public function getTrace()
	{
		if ($this->trace === null) {
			$this->trace = 0;
			for ($i = 0; $i < $this->getRowCount(); ++$i) {
				$this->trace += $this->getElement($i, $i);
			}
		}
		return $this->trace;
	}
	
	/**
	 * Gets the Square Matrix determinant
	 * @return float
	 */
	public function getDeterminant()
	{
		if ($this->determinant === null) {
			$this->determinant = 0;
			if ($this->getFactorialN() === 2) {
				$this->determinant = $this->getElement(0, 0) * $this->getElement(1, 1) - $this->getElement(0, 1) * $this->getElement(1, 0);
			} else {
				for ($j = 0; $j < $this->getColumnCount(); ++$j) {
					$subMatrix = $this->getSubMatrix(0, $j);
					if (fmod($j, 2) == 0) {
						$this->determinant += $this->getElement(0, $j) * $subMatrix->getDeterminant();
					} else {
						$this->determinant -= $this->getElement(0, $j) * $subMatrix->getDeterminant();
					}
				}
			}
		}
		return $this->determinant;
	}
	
	/**
	 * Gets the Square Matrix Identity Matrix
	 * @return SquareMatrix
	 */
	public function getIdentity()
	{
		if ($this->identity === null) {
			$class = get_class($this);
			$this->identity = new $class($this->getRowCount());
			for ($i = 0; $i < $this->getRowCount(); ++$i) {
				$this->identity->setElement($i, $i, 1);
			}
		}
		return $this->identity;
	}

	/**
	 * If possible, inverts the Square Matrix 
	 * @throws Exception
	 * @return SquareMatrix
	 */
	public function inverse()
	{
		$inverse = array();
		$determinant = $this->getDeterminant();
		if ($determinant == 0) {
			throw new Exception('Square Matrix isn\'t invertible due to determinant equal to 0');
		}
		
		$coeficient = 1/$determinant;
		for ($i = 0; $i < $this->getRowCount(); ++$i) {
			$inverse[$i] = array();
			for ($j = 0; $j < $this->getColumnCount(); ++$j) {
				$subMatrix = $this->getSubMatrix($i, $j);
				$value = $coeficient * $subMatrix->getDeterminant();
				if (fmod($i + $j, 2) == 0) {
					$inverse[$i][$j] = $value;
				} else {
					$inverse[$i][$j] = -$value;
				}
			}
		}

		$this->setData($inverse);
		$this->transpose();
		return $this;
	}
		
	/**
	 * Returns a Square Matrix to the power of n
	 * @static
	 * @param SquareMatrix $squareMatrix
	 * @param int $n
	 * @throws Exception
	 * @return SquareMatrix
	 */
	public static function power(SquareMatrix $squareMatrix, $n)
	{
		if (!is_int($n)) {
			throw new Exception('Invalid param - $n must be integer');
		}

		$result = $squareMatrix->getIdentity();
		for ($i = 0; $i < $n; ++$i) {
			$result = parent::product($result, $squareMatrix);
		}
		return $result;
	}

	/**
	 * Returns the exponential of the given Square Matrix
	 * @static
	 * @param SquareMatrix $squareMatrix
	 * @return SquareMatrix
	 */
	public static function exponential(SquareMatrix $squareMatrix)
	{
		$factorial = 1;
		$result = $squareMatrix->getIdentity();
		for ($n = 1; $n < 22; ++$n) {
			$matrix = self::power($squareMatrix, $n);
			$matrix->divide($factorial);
			$result = parent::addition($result, $matrix);
			$factorial *= $n;
		}
		return $result;
	}
	
	/**
	 * Resets the previously calculated SquareMatrix and Matrix properties.
	 * @see Matrix::resetProperties()
	 */
	protected function resetProperties()
	{	
		$this->trace = null;
		$this->identity = null;
		$this->factorialN = null;
		$this->determinant = null;
		
		parent::resetProperties();
	}
}