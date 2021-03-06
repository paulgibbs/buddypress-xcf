<?php

/**
 * @group core
 * @group nav
 */
class BP_Tests_Core_Nav_BpCoreRemoveSubnavItem extends BP_UnitTestCase {
	/**
	 * @expectedIncorrectUsage bp_nav
	 */
	public function test_should_remove_subnav_item() {
		$bp = buddypress();

		$_bp_nav = $bp->bp_nav;
		$_bp_options_nav = $bp->bp_options_nav;

		bp_core_new_nav_item( array(
			'name' => 'Foo',
			'slug' => 'foo',
			'screen_function' => 'foo',
		) );

		bp_core_new_subnav_item( array(
			'name' => 'Bar',
			'slug' => 'bar',
			'parent_slug' => 'foo',
			'parent_url' => 'foo',
			'screen_function' => 'bar',
		) );

		$this->assertTrue( isset( $bp->bp_options_nav['foo']['bar'] ) );

		bp_core_remove_subnav_item( 'foo', 'bar' );

		$this->assertFalse( isset( $bp->bp_options_nav['foo']['bar'] ) );

		$bp->bp_nav = $_bp_nav;
		$bp->bp_options_nav = $_bp_options_nav;
	}

	/**
	 * @expectedIncorrectUsage bp_nav
	 */
	public function test_should_fail_on_incorrect_parent() {
		$bp = buddypress();

		$_bp_nav = $bp->bp_nav;
		$_bp_options_nav = $bp->bp_options_nav;

		bp_core_new_nav_item( array(
			'name' => 'Foo',
			'slug' => 'foo',
			'screen_function' => 'foo',
		) );

		bp_core_new_subnav_item( array(
			'name' => 'Bar',
			'slug' => 'bar',
			'parent_slug' => 'foo',
			'parent_url' => 'foo',
			'screen_function' => 'bar',
		) );

		$this->assertTrue( isset( $bp->bp_options_nav['foo']['bar'] ) );

		bp_core_remove_subnav_item( 'bad-parent', 'bar' );

		$this->assertTrue( isset( $bp->bp_options_nav['foo']['bar'] ) );

		$bp->bp_nav = $_bp_nav;
		$bp->bp_options_nav = $_bp_options_nav;
	}

	public function test_backcompat_remove_group_nav_items() {
		$g1 = $this->factory->group->create();

		// In group context
		$g_obj = groups_get_group( array( 'group_id' => $g1 ) );
		$this->go_to( bp_get_group_permalink( $g_obj ) );

		bp_core_new_subnav_item( array(
			'name' => 'Clam',
			'slug' => 'clam',
			'parent_slug' => bp_get_current_group_slug(),
			'parent_url' => bp_get_group_permalink( $g_obj ),
			'screen_function' => 'clam_subnav',
		) );

		bp_core_remove_subnav_item( $g_obj->slug, 'clam' );

		$nav = bp_get_nav_menu_items( 'groups' );
		$found = false;
		foreach ( $nav as $_nav ) {
			if ( 'clam' === $_nav->css_id ) {
				$found = true;
				break;
			}
		}

		$this->assertFalse( $found );
	}
}
