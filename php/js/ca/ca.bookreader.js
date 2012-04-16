/* ----------------------------------------------------------------------
 * js/ca/ca.bookreader.js
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2011 Whirl-i-Gig
 *
 * For more information visit http://www.CollectiveAccess.org
 *
 * This program is free software; you may redistribute it and/or modify it under
 * the terms of the provided license as published by Whirl-i-Gig
 *
 * CollectiveAccess is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTIES whatsoever, including any implied warranty of 
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
 *
 * This source code is free and modifiable under the terms of 
 * GNU General Public License. (http://www.gnu.org/copyleft/gpl.html). See
 * the "license.txt" file for details, or visit the CollectiveAccess web site at
 * http://www.CollectiveAccess.org
 *
 * ----------------------------------------------------------------------
 */
 
var caUI = caUI || {};

(function ($) {
	caUI.initBookReader = function(options) {
		// --------------------------------------------------------------------------------
		// setup options
		var that = jQuery.extend({
			bookreader: null,
			pages: [],
			imagesBaseUrl: null,
			initialPage : 1,
			bookTitle: '',
			bookUrl: '',
			downloadIconImgHTML: null,
			downloadUrl: null,
			closeIconImgHTML: null
		}, options);
		
		that.bookreader = new BookReader();
		
		that.bookreader.getPageWidth = function(index) {
			return that.pages[index] ? that.pages[index]['pageWidth'] : null;
		}
		
		that.bookreader.getPageHeight = function(index) {
			return that.pages[index] ? that.pages[index]['pageHeight'] : null;
		}
		
		that.bookreader.getPageURI = function(index, reduce, rotate) {
			return that.pages[index] ? that.pages[index]['pageUrl'] : null;
		}
		
		var gMaxPageWidth = 0;
		that.bookreader.getMaxPageWidth = function() {
			if(gMaxPageWidth) return gMaxPageWidth;
			var i;
			var gMaxPageWith = 0;
			for(i=0; i < that.pages.length; i++) {
				if (that.pages[i]['pageWidth'] > gMaxPageWidth) { gMaxPageWidth = that.pages[i]['pageWidth']; }
			}
			
			return gMaxPageWidth;
		}
		
		var gMaxPageHeight = 0;
		that.bookreader.getMaxPageHeight = function() {
			if (gMaxPageHeight) return gMaxPageHeight;
			var i;
			var gMaxPageHeight = 0;
			for(i=0; i < that.pages.length; i++) {
				if (that.pages[i]['pageHeight'] > gMaxPageHeight) { gMaxPageHeight = that.pages[i]['pageHeight']; }
			}
			
			return gMaxPageHeight;
		}
		
		// Return which side, left or right, that a given page should be displayed on
		that.bookreader.getPageSide = function(index) {
			if (0 == (index & 0x1)) {
				return 'R';
			} else {
				return 'L';
			}
		}
		
		that.bookreader.getSpreadIndices = function(pindex) {   
			var spreadIndices = [null, null]; 
			if ('rl' == this.pageProgression) {
				// Right to Left
				if (this.getPageSide(pindex) == 'R') {
					spreadIndices[1] = pindex;
					spreadIndices[0] = pindex + 1;
				} else {
					// Given index was LHS
					spreadIndices[0] = pindex;
					spreadIndices[1] = pindex - 1;
				}
			} else {
				// Left to right
				if (this.getPageSide(pindex) == 'L') {
					spreadIndices[0] = pindex;
					spreadIndices[1] = pindex + 1;
				} else {
					// Given index was RHS
					spreadIndices[1] = pindex;
					spreadIndices[0] = pindex - 1;
				}
			}
			
			return spreadIndices;
		}
		
		that.bookreader.getPageNum = function(index) {
			if(that.pages[index]) {
				jQuery("#BRtoolbarPageTitleDisplay").html(that.pages[index]['pageTitle'] ? that.pages[index]['pageTitle'] : '');
			}
			return index+1;
		}
		
		// Total number of leafs
		that.bookreader.numLeafs = that.pages.length;
		that.bookreader.mode = 1;
		that.bookreader.reduce = 1;
		
		// Override the path used to find UI images
		that.bookreader.imagesBaseURL = that.imagesBaseUrl;
		
		that.bookreader.getEmbedCode = function(frameWidth, frameHeight, viewParams) {
			return "Embed code not supported";
		}
		
		that.bookreader.bookTitle = that.bookTitle;
		that.bookreader.bookUrl = that.bookUrl;
		
		
		// override toolbar layout in IA BookReader with our own layout
		that.bookreader.initToolbar = function(mode, ui) {
			var toolbarHTML = "<div id='BRtoolbar'>";
			
			toolbarHTML += "<div id='BRtoolbarDownloadButton'><a href='#' id='BRtoolbarDownloadLink'>" + that.downloadIconImgHTML + "</a></div>";
			toolbarHTML += "<div id='BRtoolbarTitleDisplay'>" + that.bookTitle + "</div>";
			toolbarHTML += "<div id='BRtoolbarPageTitleDisplay'>-</div>";
			toolbarHTML += "<div id='BRtoolbarCloseButton' class='close'>" + that.closeIconImgHTML + "</div>";
			toolbarHTML += "<div id='BRtoolbarPageDisplay'>?/?</div>";
			
			toolbarHTML += "</div>";
			
			jQuery("#BookReader").append(toolbarHTML);
			
			if (that.downloadUrl) {
				jQuery('#BRtoolbarDownloadLink').attr('href', that.downloadUrl);
			}
		}
		
		// update current page display in toolbar 
		that.bookreader.onPageChange = function(currentPage, numPages) {
			jQuery("#BRtoolbarPageDisplay").html(currentPage + "/" + numPages);
		}
		
		that.bookreader.init({ index: (that.initialPage > 0) ? (that.initialPage - 1)  : 0});
		
		// --------------------------------------------------------------------------------
		// Define methods
		// --------------------------------------------------------------------------------
		that.setPages = function(pageArray) {
			that.pages = pageArray;
			that.bookreader.numLeafs = that.pages.length;
		}
		
		that.getPages = function() {
			return that.pages;
		}
		
		that.numPages = function() {
			return that.bookreader.numLeafs;
		}
		// --------------------------------------------------------------------------------
		
		return that;
	};	
})(jQuery);
