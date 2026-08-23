/**
 * Pure JavaScript SmartMenu Implementation
 * Replicates SmartMenus jQuery functionality
 */

(function() {
    'use strict';

    function SmartMenu(menuId) {
        this.menu = document.getElementById(menuId);
        if (!this.menu) return;

        this.init();
    }

    SmartMenu.prototype.init = function() {
        var self = this;
        var menuItems = this.menu.querySelectorAll('li');

        // Add classes and setup
        Array.prototype.forEach.call(menuItems, function(item) {
            var submenu = item.querySelector('ul');
            
            if (submenu) {
                item.classList.add('has-submenu');
                submenu.classList.add('sub-menu');
                
                // Add arrow element to the link
                var link = item.querySelector('a');
                var arrow = null;
                if (link) {
                    // Check if arrow already exists
                    var existingArrow = link.querySelector('.sub-arrow');
                    if (!existingArrow) {
                        arrow = document.createElement('span');
                        arrow.className = 'sub-arrow';
                        link.appendChild(arrow);
                    } else {
                        arrow = existingArrow;
                    }
                }
                
                // Handle hover (desktop)
                item.addEventListener('mouseenter', function(e) {
                    if (window.innerWidth > 768) {
                        self.showSubmenu(item, submenu);
                    }
                });
                
                item.addEventListener('mouseleave', function(e) {
                    if (window.innerWidth > 768) {
                        self.hideSubmenu(item, submenu);
                    }
                });

                // Handle click on arrow (mobile and desktop)
                if (arrow) {
                    arrow.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        self.toggleSubmenu(item, submenu);
                    });
                }

                // Handle click on link (mobile only - to prevent navigation)
                if (link) {
                    link.addEventListener('click', function(e) {
                        // If clicking on the arrow, let arrow handler take care of it
                        if (e.target.classList.contains('sub-arrow') || e.target.closest('.sub-arrow')) {
                            return;
                        }
                        // On mobile, prevent navigation if there's a submenu
                        if (window.innerWidth <= 768 && submenu) {
                            e.preventDefault();
                            self.toggleSubmenu(item, submenu);
                        }
                    });
                }
            }
        });

        // Close submenus when clicking outside
        document.addEventListener('click', function(e) {
            if (!self.menu.contains(e.target)) {
                self.closeAllSubmenus();
            }
        });

        // Handle window resize
        window.addEventListener('resize', function() {
            self.closeAllSubmenus();
        });
    };

    SmartMenu.prototype.showSubmenu = function(item, submenu) {
        // Close other submenus at the same level
        this.closeSiblingSubmenus(item);
        
        // Show this submenu
        item.classList.add('active');
        submenu.classList.add('active');
        submenu.style.display = 'block';
        
        // Add highlighted class to link for arrow change
        var link = item.querySelector('a');
        if (link) {
            link.classList.add('highlighted');
        }
        
        // Position submenu
        this.positionSubmenu(item, submenu);
    };

    SmartMenu.prototype.hideSubmenu = function(item, submenu) {
        // Only hide if not hovering over submenu
        var self = this;
        setTimeout(function() {
            if (!item.matches(':hover') && !submenu.matches(':hover')) {
                item.classList.remove('active');
                submenu.classList.remove('active');
                submenu.style.display = '';
                
                // Remove highlighted class from link
                var link = item.querySelector('a');
                if (link) {
                    link.classList.remove('highlighted');
                }
            }
        }, 100);
    };

    SmartMenu.prototype.toggleSubmenu = function(item, submenu) {
        var link = item.querySelector('a');
        if (submenu.classList.contains('active')) {
            this.closeSubmenu(item, submenu);
        } else {
            this.closeSiblingSubmenus(item);
            item.classList.add('active');
            submenu.classList.add('active');
            submenu.style.display = 'block';
            if (link) {
                link.classList.add('highlighted');
            }
            this.positionSubmenu(item, submenu);
        }
    };

    SmartMenu.prototype.closeSubmenu = function(item, submenu) {
        item.classList.remove('active');
        submenu.classList.remove('active');
        submenu.style.display = '';
        
        // Remove highlighted class from link
        var link = item.querySelector('a');
        if (link) {
            link.classList.remove('highlighted');
        }
        
        // Close nested submenus
        var nestedSubmenus = submenu.querySelectorAll('.sub-menu');
        var self = this;
        Array.prototype.forEach.call(nestedSubmenus, function(nested) {
            nested.classList.remove('active');
            nested.style.display = '';
            var nestedItem = nested.parentElement;
            nestedItem.classList.remove('active');
            var nestedLink = nestedItem.querySelector('a');
            if (nestedLink) {
                nestedLink.classList.remove('highlighted');
            }
        });
    };

    SmartMenu.prototype.closeSiblingSubmenus = function(item) {
        var parent = item.parentElement;
        if (parent) {
            var siblings = parent.querySelectorAll('li.has-submenu');
            var self = this;
            Array.prototype.forEach.call(siblings, function(sibling) {
                if (sibling !== item) {
                    var siblingSubmenu = sibling.querySelector('.sub-menu');
                    if (siblingSubmenu) {
                        self.closeSubmenu(sibling, siblingSubmenu);
                    }
                }
            });
        }
    };

    SmartMenu.prototype.closeAllSubmenus = function() {
        var self = this;
        var allSubmenus = this.menu.querySelectorAll('.sub-menu');
        Array.prototype.forEach.call(allSubmenus, function(submenu) {
            var item = submenu.parentElement;
            self.closeSubmenu(item, submenu);
        });
    };

    SmartMenu.prototype.positionSubmenu = function(item, submenu) {
        // Reset positioning
        submenu.style.left = '';
        submenu.style.right = '';
        submenu.style.top = '';
        
        // Get positions
        var itemRect = item.getBoundingClientRect();
        var submenuRect = submenu.getBoundingClientRect();
        var viewportWidth = window.innerWidth;
        var viewportHeight = window.innerHeight;
        
        // Check if submenu is a direct child (horizontal positioning)
        var isDirectChild = item.parentElement === this.menu;
        
        if (isDirectChild) {
            // Top-level submenu - position below
            submenu.style.top = itemRect.height + 'px';
            submenu.style.left = '0';
            
            // Check if submenu goes off right edge
            if (itemRect.left + submenuRect.width > viewportWidth) {
                submenu.style.left = 'auto';
                submenu.style.right = '0';
            }
        } else {
            // Nested submenu - position to the right
            submenu.style.top = '0';
            submenu.style.left = itemRect.width + 'px';
            
            // Check if submenu goes off right edge, position to left instead
            if (itemRect.right + submenuRect.width > viewportWidth) {
                submenu.style.left = 'auto';
                submenu.style.right = itemRect.width + 'px';
            }
            
            // Check if submenu goes off bottom edge
            if (itemRect.bottom + submenuRect.height > viewportHeight) {
                submenu.style.top = 'auto';
                submenu.style.bottom = '0';
            }
        }
    };

    // Initialize when DOM is ready
    function initMenu() {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                new SmartMenu('main-menu');
            });
        } else {
            new SmartMenu('main-menu');
        }
    }

    initMenu();

})();




