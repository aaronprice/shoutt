<?= '<?xml version="1.0" encoding="UTF-8"?>' ?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
            http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
	<url>
		<loc><?= site_url() ?></loc>
		<priority>1.00</priority>
        <lastmod><?= date('Y-m-d\TH:i:s\-04:00', mktime(date('H')-1, date('i'), 0, date('n'), date('j'), date('Y'))) ?></lastmod>
		<changefreq>hourly</changefreq>
	</url>
    <url>
		<loc><?= site_url('signup') ?></loc>
		<priority>0.90</priority>
        <lastmod><?= date('Y-m-d\TH:i:s\-04:00', mktime(date('H'), date('i'), 0, date('n')-1, date('j'), date('Y'))) ?></lastmod>
		<changefreq>monthly</changefreq>
	</url>
    <url>
		<loc><?= site_url('contact') ?></loc>
		<priority>0.90</priority>
        <lastmod><?= date('Y-m-d\TH:i:s\-04:00', mktime(date('H'), date('i'), 0, date('n')-1, date('j'), date('Y'))) ?></lastmod>
		<changefreq>monthly</changefreq>
	</url>
    <?php foreach($stories as $story) : ?>
    <?php 
		$category 	= ($story['subcat'] == 'all') ? $story['category'] : $story['subcat']; 
		$year 		= date('Y', $story['datesubmitted']);
		$month 		= date('m', $story['datesubmitted']);
		$day 		= date('d', $story['datesubmitted']);
	?>
	<url>
		<loc><?= site_url($category.'/'.$year.'/'.$month.'/'.$day.'/'.$story['headline']) ?></loc>
		<priority>0.70</priority>
        <lastmod><?= date('Y-m-d\TH:i:s\-04:00', $story['activity_time']) ?></lastmod>
		<changefreq>hourly</changefreq>
	</url>
    <?php endforeach; ?>
</urlset>