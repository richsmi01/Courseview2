<?php
/**
 * Register the ElggBlog class for the object/blog subtype
 */

if (get_subtype_id('object', 'cvmenu')) {
	update_subtype('object', 'cvmenu');
} else {
	add_subtype('object', 'cvmenu');
}

if (get_subtype_id('object', 'cvcourse')) {
	update_subtype('object', 'cvcourse');
} else {
	add_subtype('object', 'cvcourse');
}
