<?php

/**
 * Mock Collection.
 *
 * This collection quickly allows you to create trees of nodes.
 * Children are specified as an array.
 *
 * Every key a filename, every array value is either:
 *   * an array, for a sub-collection
 *   * a string, for a file
 *   * An instance of Sabre_DAV_INode.
 *
 * @copyright Copyright (C) 2007-2014 fruux GmbH. All rights reserved.
 * @author Evert Pot (http://evertpot.com/)
 * @license http://sabre.io/license/ Modified BSD License
 */
class Sabre_DAV_Mock_Collection extends Sabre_DAV_Collection {

    protected $name;
    protected $children;

    /**
     * Creates the object
     *
     * @param string $name
     * @param array $children
     * @return void
     */
    public function __construct($name, array $children = array()) {

        $this->name = $name;
        $this->children = $children;

    }

    /**
     * Returns the name of the node.
     *
     * This is used to generate the url.
     *
     * @return string
     */
    public function getName() {

        return $this->name;

    }

    /**
     * Creates a new file in the directory
     *
     * Data will either be supplied as a stream resource, or in certain cases
     * as a string. Keep in mind that you may have to support either.
     *
     * After successful creation of the file, you may choose to return the ETag
     * of the new file here.
     *
     * The returned ETag must be surrounded by double-quotes (The quotes should
     * be part of the actual string).
     *
     * If you cannot accurately determine the ETag, you should not return it.
     * If you don't store the file exactly as-is (you're transforming it
     * somehow) you should also not return an ETag.
     *
     * This means that if a subsequent GET to this new file does not exactly
     * return the same contents of what was submitted here, you are strongly
     * recommended to omit the ETag.
     *
     * @param string $name Name of the file
     * @param resource|string $data Initial payload
     * @return null|string
     */
    public function createFile($name, $data = null) {

        if (is_resource($data)) {
            $data = stream_get_contents($data);
        }
        $this->children[$name] = $data;
        return '"' . md5($data) . '"';

    }

    /**
     * Creates a new subdirectory
     *
     * @param string $name
     * @return void
     */
    public function createDirectory($name) {

        $this->children[$name] = array();

    }

    /**
     * Returns an array with all the child nodes
     *
     * @return Sabre_DAV_INode[]
     */
    public function getChildren() {

        $result = array();
        foreach($this->children as $key=>$value) {

            if ($value instanceof Sabre_DAV_INode) {
                $result[] = $value;
            } elseif (is_array($value)) {
                $result[] = new Sabre_DAV_Mock_Collection($key, $value);
            } else {
                $result[] = new Sabre_DAV_Mock_File($key, $value);
            }

        }

        return $result;

    }

}