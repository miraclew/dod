<?php
/**
 * Html 标记生成
 */
class HtmlTag {
	public static function room_link($id, $title) {
		return "<a href=\"link://room/$id\">$title</a>";
	}
	
	public static function user_link($id, $nickname) {
		return "<a href=\"link://user/$id\">$nickname</a>";
	}
	
	public static function audio($url, $duration) {
		return "<audio src=\"$url\" duration=$duration></audio>";
	}
	
	public static function image($url) {
		return "<image src=\"$url\" />";
	}	
}