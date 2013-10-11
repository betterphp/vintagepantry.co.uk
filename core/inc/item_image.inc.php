<?php

/**
 * Represents an image of an item.
 */
class item_image {
	
	/**
	 * @var int The ID of the image.
	 */
	private $id;
	
	/**
	 * @var string The folder the images are stored in.
	 */
	private $dir;
	
	const PREVIEW_WIDTH		= 341;
	const PREVIEW_HEIGHT	= 255;
	
	const COVER_WIDTH		= 180;
	const COVER_HEIGHT		= 135;
	
	const THUMB_WIDTH		= 80;
	const THUMB_HEIGHT		= 60;
	
	public function __construct($item_id, $image_id){
		$this->id = $image_id;
		$this->dir = "core/user_content/item_images/{$item_id}";
	}
	
	/**
	 * Gets the name of the full size image.
	 * 
	 * @return string The name
	 */
	public function get_full_name(){
		return "{$this->id}_full.png";
	}
	
	/**
	 * Gets the name of the preview image.
	 * 
	 * @return string The name.
	 */
	public function get_preview_name(){
		return "{$this->id}_preview.png";
	}
	
	/**
	 * Gets the name of the cover image.
	 *
	 * @return string The name.
	 */
	public function get_cover_name(){
		return "{$this->id}_cover.png";
	}
	
	/**
	 * Gets the name of the thumbnail image.
	 * 
	 * @return string The name.
	 */
	public function get_thumb_name(){
		return "{$this->id}_thumb.png";
	}
	
	/**
	 * Gets the relative path to the full size image, for use in the src attribute of an img tag.
	 * 
	 * @return string The path.
	 */
	public function get_full_relative_path(){
		return $this->dir . '/' . $this->get_full_name();
	}
	
	/**
	 * Gets the relative path to the preview image, for use in the src attribute of an img tag.
	 * 
	 * @return string The path.
	 */
	public function get_preview_relative_path(){
		return $this->dir . '/' . $this->get_preview_name();
	}
	
	/**
	 * Gets the relative path to the cover image, for use in the src attribute of an img tag.
	 * 
	 * @return string The path
	 */
	public function get_cover_relative_path(){
		return $this->dir . '/' . $this->get_cover_name();
	}
	
	/**
	 * Gets the relative path to the thumbnail image, for use in the src attribute of an img tag.
	 * 
	 * @return string The path.
	 */
	public function get_thumb_relative_path(){
		return $this->dir . '/' . $this->get_thumb_name();
	}
	
}

?>