<?php

interface IMenuProvider {
	public static function getMenuItems();
}

interface IPageProvider {
	public static function getPage();
}

interface IPresenterMappingProvider {
	public static function getPresenterMapping();
}
