/*
Tablesorter settings/directives
Version: 1.0.1
Original code: Tablesorter docs
Copyright: See notice in adrotate-pro.php
*/
jQuery(function() {
	jQuery("table.manage-ads-main").tablesorter({
		headers: {
			3: { sorter: false },
			5: { sorter: false },
			6: { sorter: false },
			8: { sorter: false },
			9: { sorter: false }
		}
	});
	jQuery("table.manage-ads-disabled").tablesorter({
		headers: {
			3: { sorter: false },
			4: { sorter: false },
			5: { sorter: false },
			6: { sorter: false }
		}
	});
	jQuery("table.manage-ads-error").tablesorter({
		headers: {
			3: { sorter: false }
		}
	});
	jQuery("table.manage-ads-archived").tablesorter({
		headers: {
			3: { sorter: false },
		}
	});
	jQuery("table.manage-groups-main").tablesorter({
		headers: {
			2: { sorter: false },
			3: { sorter: false },
			4: { sorter: false },
			5: { sorter: false },
			6: { sorter: false },
			7: { sorter: false }
		}
	});
	jQuery("table.manage-schedules-main").tablesorter({
		headers: {
			3: { sorter: false },
			4: { sorter: false },
			5: { sorter: false }
		}
	});
	jQuery("table.manage-advertisers-main").tablesorter({
		headers: {
		}
	});
	jQuery("table.moderate-queue").tablesorter({
		headers: {
			3: { sorter: false }
		}
	});
	jQuery("table.moderate-rejected").tablesorter({
		headers: {
			3: { sorter: false }
		}
	});
});
