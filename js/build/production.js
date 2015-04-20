/*global jQuery */
/*jshint browser:true */
/*!
 * FitVids 1.1
 *
 * Copyright 2013, Chris Coyier - http://css-tricks.com + Dave Rupert - http://daverupert.com
 * Credit to Thierry Koblentz - http://www.alistapart.com/articles/creating-intrinsic-ratios-for-video/
 * Released under the WTFPL license - http://sam.zoy.org/wtfpl/
 *
 */

;(function( $ ){

    'use strict';

    $.fn.fitVids = function( options ) {
        var settings = {
            customSelector: null,
            ignore: null
        };

        if(!document.getElementById('fit-vids-style')) {
            // appendStyles: https://github.com/toddmotto/fluidvids/blob/master/dist/fluidvids.js
            var head = document.head || document.getElementsByTagName('head')[0];
            var css = '.fluid-width-video-wrapper{width:100%;position:relative;padding:0;}.fluid-width-video-wrapper iframe,.fluid-width-video-wrapper object,.fluid-width-video-wrapper embed {position:absolute;top:0;left:0;width:100%;height:100%;}';
            var div = document.createElement("div");
            div.innerHTML = '<p>x</p><style id="fit-vids-style">' + css + '</style>';
            head.appendChild(div.childNodes[1]);
        }

        if ( options ) {
            $.extend( settings, options );
        }

        return this.each(function(){
            var selectors = [
                'iframe[src*="player.vimeo.com"]',
                'iframe[src*="youtube.com"]',
                'iframe[src*="youtube-nocookie.com"]',
                'iframe[src*="kickstarter.com"][src*="video.html"]',
                'object',
                'embed'
            ];

            if (settings.customSelector) {
                selectors.push(settings.customSelector);
            }

            var ignoreList = '.fitvidsignore';

            if(settings.ignore) {
                ignoreList = ignoreList + ', ' + settings.ignore;
            }

            var $allVideos = $(this).find(selectors.join(','));
            $allVideos = $allVideos.not('object object'); // SwfObj conflict patch
            $allVideos = $allVideos.not(ignoreList); // Disable FitVids on this video.

            $allVideos.each(function(){
                var $this = $(this);
                if($this.parents(ignoreList).length > 0) {
                    return; // Disable FitVids on this video.
                }
                if (this.tagName.toLowerCase() === 'embed' && $this.parent('object').length || $this.parent('.fluid-width-video-wrapper').length) { return; }
                if ((!$this.css('height') && !$this.css('width')) && (isNaN($this.attr('height')) || isNaN($this.attr('width'))))
                {
                    $this.attr('height', 9);
                    $this.attr('width', 16);
                }
                var height = ( this.tagName.toLowerCase() === 'object' || ($this.attr('height') && !isNaN(parseInt($this.attr('height'), 10))) ) ? parseInt($this.attr('height'), 10) : $this.height(),
                    width = !isNaN(parseInt($this.attr('width'), 10)) ? parseInt($this.attr('width'), 10) : $this.width(),
                    aspectRatio = height / width;
                if(!$this.attr('id')){
                    var videoID = 'fitvid' + Math.floor(Math.random()*999999);
                    $this.attr('id', videoID);
                }
                $this.wrap('<div class="fluid-width-video-wrapper"></div>').parent('.fluid-width-video-wrapper').css('padding-top', (aspectRatio * 100)+'%');
                $this.removeAttr('height').removeAttr('width');
            });
        });
    };
// Works with either jQuery or Zepto
})( window.jQuery || window.Zepto );
jQuery(document).ready(function($){

    var siteHeader = $('#site-header');
    var titleContainer = $('#title-container');
    var toggleNavigation = $('#toggle-navigation');
    var menuPrimaryContainer = $('#menu-primary-container');
    var menuPrimary = $('#menu-primary');
    var menuPrimaryItems = $('#menu-primary-items');
    var toggleDropdown = $('.toggle-dropdown');
    var toggleSidebar = $('#toggle-sidebar');
    var sidebarPrimary = $('#sidebar-primary');
    var sidebarPrimaryContent = $('#sidebar-primary-content');
    var sidebarWidgets = $('#sidebar-primary-widgets');
    var socialMediaIcons = siteHeader.find('.social-media-icons');

    // centers 2nd tier menus with their parent menu items
    centerDropdownMenus();

    // put menu into new line if touching social icons
    socialIconAdjustment();

    $(window).resize(function(){
        centerDropdownMenus();
        socialIconAdjustment();
    });

    toggleNavigation.on('click', openPrimaryMenu);

    function openPrimaryMenu() {

        if( menuPrimaryContainer.hasClass('open') ) {
            menuPrimaryContainer.removeClass('open');
            $(this).removeClass('open');
        } else {
            menuPrimaryContainer.addClass('open');
            $(this).addClass('open');
        }
    }

    // display the dropdown menus
    toggleDropdown.on('click', openDropdownMenu);

    function openDropdownMenu() {

        // get the buttons parent (li)
        var menuItem = $(this).parent();

        // if already opened
        if( menuItem.hasClass('open') ) {

            // remove open class
            menuItem.removeClass('open');

            // change screen reader text
            $(this).children('span').text('open child menu');

            // change aria text
            $(this).attr('aria-expanded', 'false');
        } else {

            // add class to open the menu
            menuItem.addClass('open');

            // change screen reader text
            $(this).children('span').text('close child menu');

            // change aria text
            $(this).attr('aria-expanded', 'true');
        }
    }

    // display the sidebar
    toggleSidebar.on('click', openSidebar);

    function openSidebar() {

        if( sidebarPrimary.hasClass('open') ) {
            sidebarPrimary.removeClass('open');

            if( $(window).width() > 899 ) {
                sidebarPrimaryContent.css('max-height', '' );
            }
        } else {
            sidebarPrimary.addClass('open');

            if( $(window).width() > 899 ) {
                sidebarPrimaryContent.css('max-height', sidebarWidgets.outerHeight() );
            }
        }
    }

    // centers 2nd tier menus with their parent menu items
    function centerDropdownMenus() {

        if( $(window).width() > 899 ) {


            var parentMenuItemsTier2 = menuPrimaryItems.children('.menu-item-has-children');

            parentMenuItemsTier2.each(function(){
                var parentWidth = $(this).width();
                var childWidth = $(this).children('ul').outerWidth();
                if( childWidth > parentWidth ) {
                    var difference = childWidth - parentWidth;
                    difference = difference / 2;
                    $(this).children('ul').css('left', -difference);
                }
            });

            var parentMenuItemsTier3 = menuPrimaryItems.find('ul ul');

            parentMenuItemsTier3.each(function(){
                var height = $(this).outerHeight();
                height = height / 2;
                var parentLink = $(this).parent().children('a');
                var parentLinkHeight = parentLink.height();
                parentLinkHeight = parentLinkHeight / 2;
                $(this).css('top', -height + parentLinkHeight);
            });
        } else {
            menuPrimaryItems.find('ul').css({
                'left': '',
                'top' : ''
            });
        }
    }

    // if menu doesn't fit next to social icons, move to next line
    function socialIconAdjustment(){

        // if at non-mobile menu width and social icons exist
        if( $(window).width() > 899 && socialMediaIcons.length > 0 ) {

            // get the width of all the header elements
            var space = siteHeader.width();
            var titleWidth = titleContainer.width() + parseInt(titleContainer.css('margin-right'));
            var menuWidth = menuPrimary.width();
            var iconsWidth = socialMediaIcons.width();

            // is there enough space? (24 extra space between menu and icons)
            if( space - titleWidth - menuWidth - iconsWidth - 24 < 0 ) {
                menuPrimaryContainer.css('display', 'block');
            } else {
                menuPrimaryContainer.css('display', 'inline-block');
            }
        } else {
            menuPrimaryContainer.css('display', '');
        }

    }
});