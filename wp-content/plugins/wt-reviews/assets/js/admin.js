jQuery(document).ready(function($) {
	// Delete photo in admin: show avatar and clear filename
	$(document).on('click', '#wt-reviews-delete-photo-btn', function(e) {
		e.preventDefault();

		var $wrap       = $('.wt-reviews-photo-display');
		var avatarUrl   = $wrap.data('avatar-url') || '';
		var $img        = $('#wt-reviews-photo-preview');
		var $filename   = $('#wt-reviews-photo-filename');
		var $deleteFlag = $('#wt-reviews-delete-photo');

		if (avatarUrl) {
			$img.attr('src', avatarUrl);
		}

		// Clear filename and mark for deletion
		$filename.val('');
		if ($deleteFlag.length) {
			$deleteFlag.val('1');
		}
	});
});
