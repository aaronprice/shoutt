<div id="sub-menu">
	<div id="top-in">
    	<?php if(false) : ?>
    	<a href="<?= $url_info['news_type_link'].$url_info['data_view'] ?>">Popular</a> | 
        <a href="<?= $url_info['news_type_link'].'/upcoming'.$url_info['data_view'] ?>">Upcoming</a>
        <?php endif; ?>
        <span>
        	<a href="/perspective">Perspective</a> | 
            <a href="/images">Images</a> | 
			<a href="/videos">Videos</a> | 
            <a href="/">List</a>
        </span>
    </div>
    Top Stories in: <a href="<?= $url_info['news_type_link'].$url_info['news_type'].'/24hours'.$url_info['data_view'] ?>">24 Hours</a> | 
        		<a href="<?= $url_info['news_type_link'].$url_info['news_type'].'/7days'.$url_info['data_view'] ?>">7 Days</a> | 
                <a href="<?= $url_info['news_type_link'].$url_info['news_type'].'/30days'.$url_info['data_view'] ?>">30 Days</a>	
</div>
<div class="menu clearfix">
<div class="rss"><a href="/rss<?= ($url_info['news_type_link'] == '/all') ? '' : $url_info['news_type_link'] ?>"><img src="/img/rss.gif" alt="RSS"/></a></div>
<?php if(true) : ?>
<ul class="topnav">
	<li><a href="/all<?= $url_info['upcoming'].$url_info['data_view'] ?>">All</a></li>
	<?php $main_menu = $this->config->item('menu'); ?>
    <?php foreach($main_menu as $main_key => $main_value) : ?>
        <li><a href="/<?= $main_key.$url_info['upcoming'].$url_info['data_view'] ?>"><?= $main_value ?></a>
			<?php $sub_menus = $this->config->item('sub_menus'); ?>
            <?php if(isset($sub_menus[$main_key])) : ?>
			<ul class="subnav">
			<?php foreach($sub_menus[$main_key] as $sub_key => $sub_value) : ?>
            <li><a href="/<?= $sub_key.$url_info['upcoming'].$url_info['data_view'] ?>"><?= $sub_value ?></a></li>
            <?php endforeach; ?>
            </ul>
			<?php endif; ?>
        </li>
    <?php endforeach; ?>
</ul>
<?php else : ?>
<ul class="topnav">
	<li id="all"><a href="/all<?= $url_info['upcoming'].$url_info['data_view'] ?>">All</a></li>
    <li id="crime"><a href="/crime<?= $url_info['upcoming'].$url_info['data_view'] ?>">Crime</a>
    	<ul class="subnav">
        	<li><a href="/murder<?= $url_info['upcoming'].$url_info['data_view'] ?>">Murder</a></li>
            <li><a href="/kidnapping<?= $url_info['upcoming'].$url_info['data_view'] ?>">Kidnapping</a></li>
            <li><a href="/burglary<?= $url_info['upcoming'].$url_info['data_view'] ?>">Burglary</a></li>
            <li><a href="/robbery<?= $url_info['upcoming'].$url_info['data_view'] ?>">Robbery</a></li>
            <li><a href="/assault<?= $url_info['upcoming'].$url_info['data_view'] ?>">Assault</a></li>
            <li><a href="/other_crime<?= $url_info['upcoming'].$url_info['data_view'] ?>">Other</a></li>
        </ul>
    </li>
    <li id="politics"><a href="/politics<?= $url_info['upcoming'].$url_info['data_view'] ?>">Politics</a>
    	<ul class="subnav">
            <li><a href="/elections<?= $url_info['upcoming'].$url_info['data_view'] ?>">Elections</a></li>
        </ul>
    </li>
    <li id="business"><a href="/business<?= $url_info['upcoming'].$url_info['data_view'] ?>">Business</a>
    	<ul class="subnav">
        	<li><a href="/economy<?= $url_info['upcoming'].$url_info['data_view'] ?>">Economy</a></li>
            <li><a href="/finance<?= $url_info['upcoming'].$url_info['data_view'] ?>">Finance</a></li>
        </ul>
    </li>
    <li id="lifestyle"><a href="/lifestyle<?= $url_info['upcoming'].$url_info['data_view'] ?>">Lifestyle</a>
    	<ul class="subnav">
        	<li><a href="/culture<?= $url_info['upcoming'].$url_info['data_view'] ?>">Culture</a></li>
            <li><a href="/health<?= $url_info['upcoming'].$url_info['data_view'] ?>">Health</a></li>
            <li><a href="/safety<?= $url_info['upcoming'].$url_info['data_view'] ?>">Safety</a></li>
            <li><a href="/religion<?= $url_info['upcoming'].$url_info['data_view'] ?>">Religion</a></li>
		</ul>
	</li>
    <li id="environment"><a href="/environment<?= $url_info['upcoming'].$url_info['data_view'] ?>">Environment</a>
    	<ul class="subnav">
        	<li><a href="/pollution<?= $url_info['upcoming'].$url_info['data_view'] ?>">Pollution</a></li>
        </ul>
    </li>
    <li id="public"><a href="/public<?= $url_info['upcoming'].$url_info['data_view'] ?>">Public</a>
    	<ul class="subnav">
        	<li><a href="/service<?= $url_info['upcoming'].$url_info['data_view'] ?>">Service</a></li>
            <li><a href="/awareness<?= $url_info['upcoming'].$url_info['data_view'] ?>">Awareness</a></li>
            <li><a href="/opinion<?= $url_info['upcoming'].$url_info['data_view'] ?>">Opinion</a></li>
        </ul>
    </li>
    <li id="features"><a href="/features<?= $url_info['upcoming'].$url_info['data_view'] ?>">Features</a>
    	<ul class="subnav">
            <li><a href="/people<?= $url_info['upcoming'].$url_info['data_view'] ?>">People</a></li>
            <li><a href="/arts<?= $url_info['upcoming'].$url_info['data_view'] ?>">Arts</a></li>
            <li><a href="/entertainment<?= $url_info['upcoming'].$url_info['data_view'] ?>">Entertainment</a></li>
            <li><a href="/carnival_2010<?= $url_info['upcoming'].$url_info['data_view'] ?>">Carnival 2010</a></li>
        </ul>
    </li>
    <li id="sports"><a href="/sports<?= $url_info['upcoming'].$url_info['data_view'] ?>">Sports</a>
    	<ul class="subnav">
        	<li><a href="/cricket<?= $url_info['upcoming'].$url_info['data_view'] ?>">Cricket</a></li>
            <li><a href="/soccer<?= $url_info['upcoming'].$url_info['data_view'] ?>">Soccer</a></li>
            <li><a href="/other_sports<?= $url_info['upcoming'].$url_info['data_view'] ?>">Other</a></li>
        </ul>
    </li>
    <li id="misc"><a href="/misc<?= $url_info['upcoming'].$url_info['data_view'] ?>">Misc</a>
    	<ul class="subnav">
        	<li><a href="/on_the_road<?= $url_info['upcoming'].$url_info['data_view'] ?>">On the Road</a></li>
        	<li><a href="/comedy<?= $url_info['upcoming'].$url_info['data_view'] ?>">Comedy</a></li>
        </ul>
    </li>
</ul>
<?php endif; ?>
</div>