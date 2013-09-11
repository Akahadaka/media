<?php if (!$modal) { ?>
<div class="region region-page-top" id="region-page-top">
	<div class="region-inner region-page-top-inner">
		{$region_page_top_inner}
	</div>
</div>  
<div class="page clearfix" id="page">
	<header id="section-header" class="section section-header">
		<div id="zone-user-wrapper" class="zone-wrapper zone-user-wrapper clearfix">  
			<div id="zone-user" class="zone zone-user clearfix container-12">
				<div class="grid-10 region region-user-first" id="region-user-first">
					<div class="region-inner region-user-first-inner">
						{$region_user_first_inner}
						&nbsp;
					</div>
				</div>
				<div class="grid-2 region region-user-second" id="region-user-second">
					<div class="region-inner region-user-second-inner">
						{$region_user_second_inner}
					</div>
				</div>
			</div>
		</div>
		<div id="zone-branding-wrapper" class="zone-wrapper zone-branding-wrapper clearfix">  
			<div id="zone-branding" class="zone zone-branding clearfix container-12">
				
			</div>
		</div>
		<div id="zone-menu-wrapper" class="zone-wrapper zone-menu-wrapper clearfix">  
  			<div id="zone-menu" class="zone zone-menu clearfix container-12">
  				<div class="grid-2 region region-branding" id="region-branding">
					<div class="region-inner region-branding-inner">
						<div class="logo-img">
							<a href="{$rootname}" rel="home" title="" class="active"><img src="{$rootname}{$theme}logo.png" alt="" id="logo" /></a>
						</div>
						{$region_branding_inner}
					</div>
				</div>
    			<div class="grid-10 region region-menu" id="region-menu">
  					<div class="region-inner region-menu-inner">
        				<nav class="navigation">
      						{$region_menu_inner}
						</nav>
					</div>
				</div>
			</div>
		</div>
	</header>    
	<section id="section-content" class="section section-content">
		<div id="zone-preface-wrapper" class="zone-wrapper zone-preface-wrapper clearfix">  
			<div id="zone-preface" class="zone zone-preface clearfix container-12">
				<div class="grid-12 region region-preface-first" id="region-preface-first">
					<div class="region-inner region-preface-first-inner">
						{$region_preface_first_inner}
					</div>
				</div>
			</div>
		</div>
		<div id="zone-content-wrapper" class="zone-wrapper zone-content-wrapper clearfix">  
			<div id="zone-content" class="zone zone-content clearfix container-12">
				<aside class="{$region_sidebar_first_grid} region region-sidebar-first" id="region-sidebar-first">
					<div class="region-inner region-sidebar-first-inner">
						{$region_sidebar_first_inner}	
					</div>
				</aside>
        		<div class="{$region_content_grid} region region-content" id="region-content">
        			<div class="region-inner region-content-inner">
        				{$region_content_inner}
					</div>
				</div>
				<aside class="{$region_sidebar_second_grid} region region-sidebar-second" id="region-sidebar-second">
					<div class="region-inner region-sidebar-second-inner">
						{$region_sidebar_second_inner}
					</div>
				</aside>
			</div>
		</div>
		<div id="zone-postscript-wrapper" class="zone-wrapper zone-postscript-wrapper clearfix">  
			<div id="zone-postscript" class="zone zone-postscript clearfix container-12">
				<div class="grid-3 region region-postscript-first" id="region-postscript-first">
					<div class="region-inner region-postscript-first-inner">
						{$region_postscript_first_inner}
					</div>
				</div>
				<div class="grid-3 region region-postscript-second" id="region-postscript-second">
					<div class="region-inner region-postscript-second-inner">
						{$region_postscript_second_inner}
					</div>
				</div>
				<div class="grid-3 region region-postscript-third" id="region-postscript-third">
					<div class="region-inner region-postscript-third-inner">
						{$region_postscript_third_inner}
					</div>
				</div>
				<div class="grid-3 region region-postscript-fourth" id="region-postscript-fourth">
					<div class="region-inner region-postscript-fourth-inner">
						{$region_postscript_fourth_inner}
					</div>
				</div>
			</div>
		</div>
	</section>
	<footer id="section-footer" class="section section-footer">
		<div id="zone-footer-wrapper" class="zone-wrapper zone-footer-wrapper clearfix">  
			<div id="zone-footer" class="zone zone-footer clearfix container-12">
				<div class="grid-12 region region-footer-first" id="region-footer-first">
					<div class="region-inner region-footer-first-inner">
						{$region_footer_first_inner}
					</div>
				</div>
			</div>
		</div>
	</footer>  
</div>
<?php } else { ?>
<div id="zone-content-wrapper" class="zone-wrapper zone-content-wrapper clearfix">  
	<div id="zone-content" class="zone zone-content clearfix container-12">
        <div class="{$region_content_grid} region region-content" id="region-content">
        	<div class="region-inner region-content-inner">
        		{$region_content_inner}
			</div>
		</div>
	</div>
</div>
<?php }?>