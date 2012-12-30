<?php
interface InterfaceRelationship
{
	public function __construct($options=array());
	public function build_association(Model $model, $attributes=array());
	public function create_association(Model $model, $attributes=array());
}

abstract class AbstractRelationship implements InterfaceRelationship
{
	
}

class HasManyRelation extends AbstractRelationship
{
	public function __construct($options = array()) {
	
	}

	public function build_association(Model $model, $attributes = array()) {
	
	}

	public function create_association(Model $model, $attributes = array()) {
		
	}
	
}

class HasOneRelation extends HasManyRelation
{
}

class ManyToManyRelation extends AbstractRelationship {
	public function __construct($options = array()) {
	
	}
	
	public function build_association(Model $model, $attributes = array()) {
	
	}
	
	public function create_association(Model $model, $attributes = array()) {
	
	}	
}