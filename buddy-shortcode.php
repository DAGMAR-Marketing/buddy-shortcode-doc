<?php

function buddy_shortcode_fn($atts) {
  // Shortcode parameters
  $atts = shortcode_atts(
      array(
        'form' => '',
        'stage-prd' => '',
        'stage-stg' => '',
        'config-path' => '',
        'pid-prd' => '',
        'pid-stg' => '',
        'ion' => '',
        'aid' => '',
        'theme' => '',
        'events' => '',
				'iframe' => 'false',
				'iframe-page' => '\/iframe-page-do-not-delete\/',
      ),
      $atts,
      'buddy'
  );

  // Domain check function
	if (function_exists('is_prod') === false) {
		function is_prod() {
			$prod = 'www.getpomi.com';
			$domain = $_SERVER['HTTP_HOST'];
			return $domain === $prod;
		}
	}

  // Determine domain-based variables
  if (is_prod() === true) {
    $configSub = '';
    if ($atts['stage-prd'] !== '') { // Parameter check
      $stage = $atts['stage-prd'];
    } else {
      $stage = 'PRODUCTION';
    }
  } else {
    $configSub = 'staging.';
    if ($atts['stage-stg'] !== '') { // Parameter check
      $stage = $atts['stage-stg'];
    } else {
      $stage = 'STAGING';
    }
  }

  // Output primary buddy script (does not change)
  $output1 = '<script src="https://js.buddy.insure/v2/index.js"></script>';

  // Determine form based on shortcode parameter
  switch ($atts['form']) {
    /**
     * ACCIDENT FORM
     * */
    case 'pp':
      // Set config path
      if ($atts['config-path'] !== '') { // Parameter check
        $configPath = $atts['config-path'];
      } else {
        $configPath = 'pomi/gaig-pomi-accident-config';
      }

      // Set partner ID
      if (is_prod() === true) {
        if ($atts['pid-prd'] !== '') { // Parameter check
          $pid = $atts['pid-prd'];
        } else {
          $pid = 'p-19g6jlnnerv7e';
        }
      } else {
        if ($atts['pid-stg'] !== '') { // Parameter check
          $pid = $atts['pid-stg'];
        } else {
          if ($atts['aid'] !== '') {
            $pid = 'p-buddytest';
          } else { // OG pp form has no aID & uses different partner ID
            $pid = 'p-test-f7vrennlj';
          }
        }
      }

      // Set ion
      if ($atts['ion'] !== '') { // Parameter check
        $ion = $atts['ion'];
      } else {
        $ion = 'GAIG_POMI_ACCIDENT';
      }

      // Set attribution ID or timestamp
      if ($atts['aid'] !== '') { // Parameter check
        $aid = 'meta: {
            partner: \'pomi\',
            attributionId: \'' . $atts['aid'] . '\'
          },';
      } else {
        $aid = 'timeStamp: Date.now()';
      }

      // Set theme
      if ($atts['theme'] !== '') { // Parameter check
        $theme = $atts['theme'];
      } else {
        $theme = 'themeBase';
      }

      // Set user events
      if ($atts['events'] !== '') { // Parameter check
        $events = $atts['events'];
      } else {
        $events = 'userEvents';
      }

			// Return form att value
			$form = 'pp';
      break;

    /**
     *  SEASON INTERRUPTION FORM
     * */
    case 'si':
    default:
      // Set config path
      if ($atts['config-path'] !== '') { // Parameter check
        $configPath = $atts['config-path'];
      } else {
        $configPath = 'si/gaig-gaig-si-config';
      }

      // Set Partner ID
      if (is_prod() === true) {
        if ($atts['pid-prd'] !== '') { // Parameter check
          $pid = $atts['pid-prd'];
        } else {
          $pid = 'p-19g6jlnnerv7e';
        }
      } else {
        if ($atts['pid-stg'] !== '') { // Parameter check
          $pid = $atts['pid-stg'];
        } else {
          $pid = 'p-buddytest';
        }
      }

      // Set ion
      if ($atts['ion'] !== '') { // Parameter check
        $ion = $atts['ion'];
      } else {
        $ion = 'GAIG_GAIG_SI';
      }

      // Set attribution ID
      if ($atts['aid'] !== '') { // Parameter check
        $aid = 'meta: {
            partner: \'pomi\',
            attributionId: \'' . $atts['aid'] . '\'
          },';
      } else {
        $aid = 'meta: {
            partner: \'pomi\',
          },';
      }

      // Set theme
      if ($atts['theme'] !== '') { // Parameter check
        $theme = $atts['theme'];
      } else {
        $theme = 'themePomi';
      }

      // Set user events
      if ($atts['events'] !== '') { // Parameter check
        $events = $atts['events'];
      } else {
        $events = 'pomiUserEvents';
      }

			// Return form att value
			$form = 'si';
      break;
  }

  // Output script & div for Buddy offer
  $output2 = '<script src="https://' . $configSub . 'embed.buddy.insure/gaig/' . $configPath . '.js"></script>';

	$output3 = '<script type="application/javascript">
    var options = {
      partnerID: \'' . $pid . '\',
      stage: \'' . $stage . '\',
      ion: \'' . $ion . '\',
      data: {
        policy: {
          ' . $aid . '
        },
      },
    };
    options.theme = config.' . $theme . ';
    options.onUserEvent = config.' . $events . ';
    window.Buddy.createOffer(options);
  </script>';

	$output4 = '<div id="buddy_offer"></div>';

	if ($atts['iframe'] === 'true') {
		$shortcodeID = uniqid('buddy-');
		ob_start(); ?>
		<div id="<?= $shortcodeID; ?>"></div>
		<script>
			function createIframeWithContent(content) {
				const container = document.querySelector('#<?= $shortcodeID; ?>');

				// Create an iframe element
				const iframe = document.createElement('iframe');
				// Set attributes (e.g., border, dimensions)
				iframe.setAttribute('id', 'buddy-<?= $form; ?>');
				iframe.setAttribute('class', 'buddy-container');
				iframe.src = '<?= $atts['iframe-page']; ?>';

				// Append iframe to the container
				container.appendChild(iframe);

				// Wait for the iframe to load, then set its content
				iframe.onload = function() {
					// Access the iframe's document and write content
					const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
					iframeDoc.open();
					iframeDoc.write(content);
					iframeDoc.close();
				};
			}

			(function() {
				const buddyOfferContent1 = <?= json_encode($output1); ?>;
				const buddyOfferContent2 = <?= json_encode($output2); ?>;
				const buddyOfferContent3 = <?= json_encode($output3); ?>;
				const buddyOfferContent4 = <?= json_encode($output4); ?>;

				// Custom content to be injected into the iframe
				const customContent = `
					<!DOCTYPE html>
					<html lang="en">
						<head>
							<meta charset="UTF-8">
							<title>Custom Iframe</title>
						</head>
						<body>
							<div> ${ buddyOfferContent1 } </div>
							<div> ${ buddyOfferContent2 } </div>
							<div> ${ buddyOfferContent3 } </div>
							<div> ${ buddyOfferContent4 } </div>
						</body>
					</html>
				`;

				// Call the function to create an iframe
				createIframeWithContent(customContent);
			})();

		</script>
		<?php return ob_get_clean();
	} else {
		return $output1 . $output2 . $output3 . $output4;
	}
}

add_shortcode('buddy', 'buddy_shortcode_fn');