<?php

namespace NNGrad\T3pimper\Domain\Model;


class FileReference extends \TYPO3\CMS\Extbase\Domain\Model\FileReference {

	/**
	 * imgvariants
	 *
	 * @var string
	 */
	protected $imgvariants;

	
	/**
	 * Returns the imgvariants
	 *
	 * @return string $imgvariants
	 */
	public function getImgvariants() {
		return $this->imgvariants;
	}

	/**
	 * Sets the imgvariants
	 *
	 * @param string $imgvariants
	 * @return void
	 */
	public function setImgvariants($imgvariants) {
		$this->imgvariants = $imgvariants;
	}

}

?>