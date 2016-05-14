<?php
namespace gossi\swagger;

use gossi\swagger\parts\TypePart;
use gossi\swagger\parts\DescriptionPart;
use gossi\swagger\parts\ItemsPart;
use gossi\swagger\parts\ExtensionPart;
use gossi\swagger\parts\ExternalDocsPart;
use phootwork\collection\CollectionUtils;
use gossi\swagger\parts\RefPart;
use phootwork\collection\ArrayList;
use gossi\swagger\collections\Definitions;
use phootwork\lang\Arrayable;
use phootwork\collection\Map;

class Schema extends AbstractModel implements Arrayable {

	use RefPart;
	use TypePart;
	use DescriptionPart;
	use ItemsPart;
	use ExternalDocsPart;
	use ExtensionPart;

	/** @var string */
	private $discriminator;

	/** @var boolean */
	private $readOnly = false;

	/** @var string */
	private $title;

	private $xml;

	/** @var string */
	private $example;

	/** @var ArrayList|boolean */
	private $required;

	/** @var Definitions */
	private $properties;

	/** @var ArrayList */
	private $allOf;

	/** @var Schema */
	private $additionalProperties;

	public function __construct($contents = null) {
		$this->parse($contents === null ? new Map() : $contents);
	}

	private function parse($contents = []) {
		$data = CollectionUtils::toMap($contents);

		$this->title = $data->get('title');
		$this->discriminator = $data->get('discriminator');
		$this->readOnly = $data->has('readOnly') && $data->get('readOnly');
		$this->example = $data->get('example');
		$this->required = $data->get('required');
		$this->properties = new Definitions($data->get('properties'));
		if ($data->has('additionalProperties')) {
			$this->additionalProperties = new Schema($data->get('additionalProperties'));
		}

		$this->allOf = new ArrayList();
		if ($data->has('allOf')) {
			foreach ($data->get('allOf') as $schema) {
				$this->allOf->add(new Schema($schema));
			}
		}

		// parts
		$this->parseRef($data);
		$this->parseType($data);
		$this->parseDescription($data);
		$this->parseItems($data);
		$this->parseExternalDocs($data);
		$this->parseExtensions($data);
	}

	public function toArray() {
		return $this->export('title', 'discriminator', 'description', 'readOnly', 'example',
				'externalDocs', $this->getTypeExportFields(), 'items', 'required',
				'properties', 'additionalProperties', 'allOf');
	}

	/**
	 *
	 * @return boolean|array
	 */
	public function getRequired() {
		return $this->required;
	}

	/**
	 *
	 * @param boolean|array $required
	 * @return $this
	 */
	public function setRequired($required) {
		$this->required = $required;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	public function getDiscriminator() {
		return $this->discriminator;
	}

	/**
	 *
	 * @param string $discriminator
	 */
	public function setDiscriminator($discriminator) {
		$this->discriminator = $discriminator;
		return $this;
	}

	/**
	 *
	 * @return boolean
	 */
	public function isReadOnly() {
		return $this->readOnly;
	}

	/**
	 *
	 * @param boolean $readOnly
	 */
	public function setReadOnly($readOnly) {
		$this->readOnly = $readOnly;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	public function getExample() {
		return $this->example;
	}

	/**
	 *
	 * @param string $example
	 */
	public function setExample($example) {
		$this->example = $example;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 *
	 * @param string $title
	 * @return $this
	 */
	public function setTitle($title) {
		$this->title = $title;
		return $this;
	}

	/**
	 *
	 * @return Definitions
	 */
	public function getProperties() {
		return $this->properties;
	}

	/**
	 *
	 * @return ArrayList
	 */
	public function getAllOf() {
		return $this->allOf;
	}

	/**
	 *
	 * @return Schema
	 */
	public function getAdditionalProperties() {
		if ($this->additionalProperties === null) {
			$this->additionalProperties = new Schema();
		}
		return $this->additionalProperties;
	}

}