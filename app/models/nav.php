<?php

class NavModel 
{
	public function getNavLinks() {
		
		$docs_links = array(
			new NavLink('Doc 1', BASEURL),
		);

		$nav_links = array();
		$nav_links[0] = new NavLink('Home', BASEURL);
		$nav_links[1] = new NavLink('About', BASEURL); 
		$nav_links[2] = new NavLink('Docs', BASEURL);
		$nav_links[3] = new NavLink('Star on Github', BASEURL);

		return $nav_links;
	}
}