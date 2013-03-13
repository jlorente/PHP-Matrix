<?php
/**
 * Class Matrix
 * 
 * This class provides a large serie of methods for manipulate Matrix objects presented in PHP as bidimensional arrays.
 * 
 * @author José Lorente Martín
 * @version 1.0
 * @copyright Copyright (c) 2012, José Lorente Martín
 */
class Matrix
{
	/**
	 * Variable to store the Two-dimensional array of n rows and m columns that compose the Matrix object.
	 * @var array
	 * @access protected
	 */
	protected $body;
	
	/**
	 * Number of rows that compose the Matrix.
	 * @var int
	 * @access protected
	 */
	protected $rowCount;

	/**
	 * Number of columns that compose the Matrix.
	 * @var int
	 * @access protected
	 */
	protected $columnCount;

	/**
	 * Variable to store the covariance Matrix of the current Matrix.
	 * @var Matrix
	 * @access protected
	 */
	protected $covariance;

	/**
	 * Variable to store the Mean Vector X of the Matrix.
	 * @var Matrix
	 * @access protected
	 */
	protected $meanVectorX;
	
	/**
	 * Variable to store the Mean Vector Y of the Matrix.
	 * @var Matrix
	 * @access protected
	 */	
	protected $meanVectorY;
	
	/**
	 * Creates a Matrix object with n rows and m columns.
	 * If $_arrayMatrix is given, fills the Matrix object with it.
	 * @param int $rows
	 * @param int $columns
	 * @param array|null $_arrayMatrix
	 * @throws Exception
	 */
	public function __construct($rows, $columns, $_arrayMatrix = null) 
	{
		$this->body = array();
		
		if (!is_int($rows) || !is_int($columns)) {
			throw new Exception('Invalid param - $rows and $columns must be integer');
		}
		
		$this->rowCount = $rows;
		$this->columnCount = $columns;	
		for ($i = 0; $i < $rows; ++$i) {
			$this->body[$i] = array_fill(0, $columns, 0);
		}		
		$this->resetProperties();
		
		if ($_arrayMatrix !== null) {
			$this->setData($_arrayMatrix);
		}
	}

	/**
	 * Sets the given value into the [$row, $column] position of the Matrix.
	 * @param int $row
	 * @param int $column
	 * @param numeric $value
	 * @throws Exception
	 */
	public function setElement($row, $column, $value)
	{
		if (!isset($this->body[$row][$column])) {
			throw new Exception("Invalid arguments - element[$row, $column] doesn't exists in matrix");
		} else {
			if (!is_numeric($value)) {
				throw new Exception('Invalid param - $value must be a numeric value');
			}
			$this->body[$row][$column] = $value;
		}		
	}
	
	/**
	 * Gets the element of the [$row, $column] position of the Matrix.
	 * @param int $row
	 * @param int $column
	 * @throws Exception
	 * @return numeric
	 */
	public function getElement($row, $column)
	{
		if (!isset($this->body[$row][$column])) {
			throw new Exception("Invalid arguments - element[$row, $column] doesn't exists in matrix");
		} else {
			return $this->body[$row][$column];
		}
	}

	/**
	 * Sets the given one-dimensional $vector into the specified $row of the Matrix.
	 * @param int $row
	 * @param array $vector
	 * @throws Exception
	 * @return void
	 */
	public function setRow($row, $vector)
	{	
		if (!is_int($row) || $row < 0 || $row >= $this->getRowCount()) {
			throw new Exception('Invalid param - $row must be integer between 0 and matrix max row iddentifier '.$this->getRowCount() - 1);
		}
		
		if (!is_array($vector)) {
			throw new Exception('Invalid param - $vector must be array');
		}
		
		try {
			for ($j = 0; $j < $this->getColumnCount(); ++$j) {
				if (isset($vector[$j])) {
					$this->setElement($row, $j, $vector[$j]);
				}
			}
		} catch (Exception $e) {
			throw new Exception("Unable to set row[$row] with given vector");
		}
		$this->resetProperties();
	}

	/**
	 * Gets the specified $row of the Matrix as a Matrix object.
	 * @param int $row
	 * @throws Exception
	 * @return Matrix
	 */
	public function getRow($row)
	{
		if (!is_int($row) || $row < 0 || $row >= $this->getRowCount()) {
			throw new Exception('Invalid param - $row must be integer between 0 and matrix max row iddentifier '.$this->getRowCount() - 1);
		} else {
			$rowMatrix = new Matrix(1, $this->getColumnCount());
			$rowMatrix->setRow(0, $this->body[$row]);
			return $rowMatrix;
		}
	}
		
	/**
	 * Sets the given one-dimensional $vector into the specified $column of the Matrix.
	 * @param int $column
	 * @param array $vector
	 * @throws Exception
	 * @return void
	 */
	public function setColumn($column, $vector)
	{
		if (!is_int($column) || $column < 0 || $column >= $this->getColumnCount()) {
			throw new Exception('Invalid param - $column must be integer between 0 and matrix max column iddentifier '.$this->getColumnCount() - 1);
		}
		
		if (!is_array($vector)) {
			throw new Exception('Invalid param - $vector must be array');
		}
		
		try {
			for ($i = 0; $i < $this->getRowCount(); ++$i) {
				if (isset($vector[$i])) {
					$this->setElement($i, $column, $vector[$i]);
				}
			}			
		} catch (Exception $e) {
			throw new Exception("Unable to set column[$column] with given vector");
		}
		$this->resetProperties();
	}

	/**
	 * Gets the specified $column of the Matrix as a Matrix object.
	 * @param int $row
	 * @throws Exception
	 * @return Matrix
	 */
	public function getColumn($column)
	{	
		if (!is_int($column) || $column < 0 || $column >= $this->getColumnCount()) {
			throw new Exception('Invalid param - $column must be integer between 0 and matrix max column iddentifier '.$this->getColumnCount() - 1);
		} else {
			for ($i = 0; $i < $this->getRowCount(); ++$i) {
				$vectorColumn[$i] = $this->getElement($i, $column);
			}
			$columnMatrix = new Matrix($this->getRowCount(), 1);
			$columnMatrix->setColumn(0, $vectorColumn);
			return $columnMatrix;
		}
	}
	
	/**
	 * Fills the Matrix with the specified two-dimensional array.
	 * @param array $_arrayMatrix
	 * @return void
	 */
	public function setData($_arrayMatrix)
	{
		if (!is_array($_arrayMatrix)) {
			throw new Exception('Invalid param - $_arrayMatrix must be array');
		}
		
		if (!is_array($_arrayMatrix[0])) {
			$this->setRow($_arrayMatrix);
		} else {
			for ($i = 0; $i < $this->getRowCount(); ++$i) {			
				$this->setRow($i, $_arrayMatrix[$i]);
			}
		}
	}
	
	/**
	 * Gets the array that compose the Matrix object.
	 * If Matrix has only one row, return array will be one-dimensional.
	 * @return array
	 */
	public function getData()
	{
		if ($this->getRowCount() === 1) {
			return $this->body[0];
		} else {
			return $this->body;
		}
	}
	
	/**
	 * Gets the number of rows that compose the Matrix object.
	 * @return int
	 */
	public function getRowCount() 
	{
		return $this->rowCount;
	}
	
	/**
	 * Gets the number of columns that compose the Matrix object.
	 * @return int
	 */
	public function getColumnCount()
	{
		return $this->columnCount;
	}

	/**
	 * Deletes the specified $row from the Matrix.
	 * @access protected
	 * @param int $row
	 * @throws Exception
	 * @return void
	 */
	protected function deleteRow($row) 
	{
		if (!is_int($row) || $row < 0 || $row >= $this->getRowCount()) {
			throw new Exception('Invalid param - $row must be integer between 0 and matrix max row iddentifier '.$this->getRowCount() - 1);
		}
		
		unset($this->body[$row]);
		$this->body = array_values($this->body);
		$this->rowCount--;
		$this->resetProperties();
	}

	/**
	 * Deletes the specified $column from the Matrix.
	 * @access protected
	 * @param int $column
	 * @throws Exception
	 * @return void
	 */
	protected function deleteColumn($column) 
	{
		if (!is_int($column) || $column < 0 || $column >= $this->getColumnCount()) {
			throw new Exception('Invalid param - $column must be integer between 0 and matrix max column iddentifier '.$this->getColumnCount() - 1);
		}
		
		for ($i = 0; $i < $this->getRowCount(); ++$i) {
			unset($this->body[$i][$column]);
			$this->body[$i] = array_values($this->body[$i]);
		}
		$this->columnCount--;
		$this->resetProperties();
	}
	
	/**
	 * Transpose the Matrix object.
	 * @return Matrix
	 */
	public function transpose() 
	{
		$body = array();
		for ($i = 0; $i < $this->getRowCount(); ++$i) {
			for ($j = 0; $j < $this->getColumnCount(); ++$j) {
				if (!isset($body[$j])) {
					$body[$j] = array();
				}
				$body[$j][$i] = $this->getElement($i, $j);
			}
		}
		$rowCount = $this->getColumnCount();
		$this->columnCount = $this->getRowCount();
		$this->rowCount = $rowCount;
		$this->body = $body;
		$this->resetProperties();
		return $this;
	}
	
	/**
	 * Gets the subMatrix of the given position [$row, $column] as a Matrix object.
	 * @param int $row
	 * @param int $column
	 * @return Matrix
	 */
	public function getSubMatrix($row, $column) 
	{
		$this->getElement($row, $column);

		$subMatrix = clone $this;
		$subMatrix->deleteRow($row);
		$subMatrix->deleteColumn($column);
		$subMatrix->resetProperties();
		return $subMatrix;
	}

	/**
	 * Gets the covariance Matrix.
	 * @return Matrix
	 */
	public function getCovariance()
	{
		if ($this->covariance === null) {
			$transposed = clone $this;
			$this->covariance = self::product($this, $transposed->transpose());
		}
		return $this->covariance;
	}
	
	/**
	 * Gets the mean row vector.
	 * @return Matrix
	 */
	public function getMeanVectorX()
	{
		if ($this->meanVectorX === null) {
			$this->meanVectorX = new Matrix(1, $this->getColumnCount());
			$meanX = array();
			for ($j = 0; $j < $this->getColumnCount(); ++$j) {
				$meanX[$j] = 0;
				for ($i = 0; $i < $this->getRowCount(); ++$i) {
					$meanX[$j] += $this->getElement($i, $j);
				}
				$meanX[$j] = $meanX[$j] !== 0 ? $meanX[$j] / $this->getRowCount() : 0;
			}
			$this->meanVectorX->setRow(0, $meanX);
		}
		return $this->meanVectorX;
	}

	/**
	 * Gets the mean column vector.
	 * @return Matrix
	 */
	public function getMeanVectorY()
	{
		if ($this->meanVectorY === null) {
			$this->meanVectorY = new Matrix($this->getRowCount(), 1);
			$meanY = array();
			for ($i = 0; $i < $this->getRowCount(); ++$i) {
				$meanY[$i] = 0;
				for ($j = 0; $j < $this->getColumnCount(); ++$j) {
					$meanY[$i] += $this->getElement($i, $j);
				}
				$meanY[$i] = $meanY[$i] !== 0 ? $meanY[$i] / $this->getRowCount() : 0;
			}
			$this->meanVectorY->setColumn(0, $meanY);
		}
		return $this->meanVectorY;
	}
	
	/**
	 * Does a scalar multiplication of the Matrix.
	 * @return void
	 */
	public function multiply($scalar)
	{
		array_walk_recursive($this->body, function(&$value, $key, $scalar) { $value *= $scalar; }, (int)$scalar);
	}
	
	/**
	 * Does a scalar division of the Matrix.
	 * @return void
	 */
	public function divide($scalar) 
	{
		array_walk_recursive($this->body, function(&$value, $key, $scalar) { $value == 0 ? 0 : $value /= $scalar; }, (int)$scalar);
	}

	/**
	 * Paints the Matrix.
	 * @param string $format Default %g&nbsp;&nbsp;&nbsp; @link http://www.php.net/manual/en/function.sprintf.php See sprintf() for more information about the format.
	 * @param string $cr Specifies the End of Line type. Default <br/>.
	 * @return void
	 */
	public function paint($format = "%g&nbsp;&nbsp;&nbsp;", $cr = "<br/>")
	{
		for ($i = 0; $i < $this->getRowCount(); ++$i) {
			for ($j = 0; $j < $this->getColumnCount(); ++$j) {
				echo sprintf($format, $this->getElement($i, $j));
			}
			echo $cr;
		}
	}
	
	/**
	 * Resets the previously calculated Matrix properties.
	 * @return void
	 */
	protected function resetProperties() 
	{
		$this->meanVectorX = $this->meanVectorY = null;
		$this->covariance = null;
	}
	
	/**
	 * Compares the size of the given matrices and returns true if both sizes are identical.
	 * @static
	 * @param Matrix $matrixA
	 * @param Matrix $matrixB
	 * @return bool
	 */
	public static function validateSameDimensions(Matrix $matrixA, Matrix $matrixB)
	{
		if ($matrixA->getRowCount() === $matrixB->getRowCount() && $matrixA->getColumnCount() === $matrixB->getColumnCount()) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Substracts $matrixB from $matrixA and returns a new Matrix object
	 * @static
	 * @param Matrix $matrixA
	 * @param Matrix $matrixB
	 * @throws Exception
	 * @return Matrix
	 */
	public static function subtraction(Matrix $matrixA, Matrix $matrixB)
	{
		if (!self::validateSameDimensions($matrixA, $matrixB)) {
			throw new Exception("Invalid matrices dimensions - \$matrixA [{$matrixA->getRowCount()}, {$matrixA->getColumnCount()}] differs from \$matrixB [{$matrixB->getRowCount()}, {$matrixB->getColumnCount()}]");
		}

		$result = new Matrix($matrixA->getRowCount(), $matrixB->getColumnCount());
		for ($i = 0; $i < $matrixA->getRowCount(); ++$i) {
			for($j = 0; $j < $matrixB->getColumnCount(); ++$j) {
				$result->setElement($i, $j, $matrixA->getElement($i, $j) - $matrixB->getElement($i, $j));
			}
		}
		return $result;
	}
	
	/**
	 * Adds $matrixB to $matrixA and returns a new Matrix object
	 * @static
	 * @param Matrix $matrixA
	 * @param Matrix $matrixB
	 * @throws Exception
	 * @return Matrix
	 */
	public static function addition(Matrix $matrixA, Matrix $matrixB)
	{
		if (!self::validateSameDimensions($matrixA,$matrixB)) {
			throw new Exception("Invalid matrices dimensions - \$matrixA [{$matrixA->getRowCount()}, {$matrixA->getColumnCount()}] differs from \$matrixB [{$matrixB->getRowCount()}, {$matrixB->getColumnCount()}]");
		}
		
		$result = new Matrix($matrixA->getRowCount(), $matrixB->getColumnCount());
		for ($i = 0; $i < $matrixA->getRowCount(); ++$i) {   
			for($j = 0; $j < $matrixB->getColumnCount(); ++$j) {
				$result->setElement($i, $j, $matrixA->getElement($i, $j) + $matrixB->getElement($i, $j));
			}
		}
		return $result;
	}
	
	/**
	 * Performs the direct sum of $matrixA and $matrixB and returns a new Matrix object
	 * @static
	 * @param Matrix $matrixA
	 * @param Matrix $matrixB
	 * @return Matrix
	 */
	public static function directSum(Matrix $matrixA, Matrix $matrixB)
	{
		$totalColumns = $matrixA->getColumnCount() + $matrixB->getColumnCount();
		$totalRows =  $matrixA->getRowCount() + $matrixB->getRowCount();
		
		$result = new Matrix($totalRows, $totalColumns);
		for ($i = 0; $i < $matrixA->getRowCount(); ++$i) {
			$result->setRow($i, array_pad($matrixA->body[$i], $totalColumns, 0));
		}		
		for (; $i < $totalRows; ++$i) {
			$result->setRow($i, array_pad($matrixB->body[$i], -$totalColumns, 0));
		}
		return $result;		
	}
		
	/**
	 * Performs the matrix product of $matrixA and $matrixB and returns a new Matrix object
	 * @static
	 * @param Matrix $matrixA
	 * @param Matrix $matrixB
	 * @return Matrix
	 */
	public static function product(Matrix $matrixA, Matrix $matrixB)
	{
		if ($matrixA->getColumnCount() !== $matrixB->getRowCount()) {
			throw new Exception("Invalid matrices dimensions - \$matrixA columns [{$matrixA->getColumnCount()}] differs from \$matrixB rows [{$matrixB->getRowCount()}]");
		}
		
		$result = new Matrix($matrixA->getRowCount(), $matrixB->getColumnCount());
		for ($i = 0; $i < $matrixA->getRowCount(); ++$i) {
			for ($j = 0; $j < $matrixB->getColumnCount(); ++$j) {
				$element = 0;
				for ($k = 0; $k < $matrixA->getColumnCount(); ++$k) {
					$element += $matrixA->getElement($i, $k) * $matrixB->getElement($k, $j);
				}
				$result->setElement($i, $j, $element);
			}
		}
		return $result;
	}

	/**
	 * Performs the kronecker matrix product of $matrixA and $matrixB and returns a new Matrix object
	 * @static
	 * @param Matrix $matrixA
	 * @param Matrix $matrixB
	 * @return Matrix
	 */
	public static function kroneckerProduct(Matrix $matrixA, Matrix $matrixB)
	{
		$result = new Matrix($matrixA->getRowCount() * $matrixB->getRowCount(), $matrixA->getColumnCount() * $matrixB->getColumnCount());
		for ($i = 0; $i < $matrixA->getRowCount(); ++$i) {
			for ($j = 0; $j < $matrixA->getColumnCount(); ++$j) {
				for ($k = 0; $k < $matrixB->getRowCount(); ++$k) {
					for ($l = 0; $l < $matrixB->getColumnCount(); ++$l) {
						$result->setElement($matrixB->getRowCount() * $i + $k, $matrixB->getColumnCount() * $j + $l, $matrixA->getElement($i, $j) * $matrixB->getElement($k, $l));
					}
				}
			}
		}
		return $result;
	}
}