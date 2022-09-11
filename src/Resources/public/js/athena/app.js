
!function ($) {
    "use strict";

    var Components = function () { };

    //initializing tooltip
    Components.prototype.initTooltipPlugin = function () {
        $.fn.tooltip && $('[data-toggle="tooltip"]').tooltip()
    },

    //initializing popover
    Components.prototype.initPopoverPlugin = function () {
        $.fn.popover && $('[data-toggle="popover"]').popover()
    },

    //initializing toast
    Components.prototype.initToastPlugin = function() {
        $.fn.toast && $('[data-toggle="toast"]').toast()
    },

    //initializing Slimscroll
    Components.prototype.initSlimScrollPlugin = function () {
        //You can change the color of scroll bar here
        $.fn.slimScroll && $(".slimscroll").slimScroll({
            height: 'auto',
            position: 'right',
            size: "8px",
            touchScrollStep: 20,
            color: '#9ea5ab'
        });
    },

    $(".filter").click(function(){
        $(".filter-group").toggle();
    });
    $('#filtername').click(function() {
        $("#inputname").toggle(this.checked);
    });
    $('#filtercompaign').click(function() {
        $("#inputcompaign").toggle(this.checked);
    });

    // Dropdown filter
    $(document).on('click.bs.dropdown.data-api', '.keep_it_open', function (e) {
        e.stopPropagation();
    });

    // Filter
    $(function() {
        $(".filter-checkbox").on("click",function() {
            var id = $(this).attr('id')
            $("." + id).toggle(this.checked);
        });
    });

    $(document).ready(function(){
        $('.card-title input[type="checkbox"]').click(function(){
            if($(this).prop("checked") == true){
                $(".flsub").css("display", "block");
            }
        });
    });

    // full screen call
    $(document).on('click', '.fullscreen-btn', function (e) {
        if (!document.fullscreenElement &&    // alternative standard method
            !document.mozFullScreenElement && !document.webkitFullscreenElement && !document.msFullscreenElement) {  // current working methods
            if (document.documentElement.requestFullscreen) {
                document.documentElement.requestFullscreen();
            } else if (document.documentElement.msRequestFullscreen) {
                document.documentElement.msRequestFullscreen();
            } else if (document.documentElement.mozRequestFullScreen) {
                document.documentElement.mozRequestFullScreen();
            } else if (document.documentElement.webkitRequestFullscreen) {
                document.documentElement.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT);
            }
        } else {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            } else if (document.msExitFullscreen) {
                document.msExitFullscreen();
            } else if (document.mozCancelFullScreen) {
                document.mozCancelFullScreen();
            } else if (document.webkitExitFullscreen) {
                document.webkitExitFullscreen();
            }
        }

    });

    //initializing form validation
    Components.prototype.initFormValidation = function () {
        $(".needs-validation").on('submit', function (event) {
            $(this).addClass('was-validated');
            if ($(this)[0].checkValidity() === false) {
                event.preventDefault();
                event.stopPropagation();
                return false;
            }
            return true;
        });
    },

    //initializing custom modal
    Components.prototype.initCustomModalPlugin = function() {
        $('[data-plugin="custommodal"]').on('click', function(e) {
            e.preventDefault();
            var modal = new Custombox.modal({
                content: {
                    target: $(this).attr("href"),
                    effect: $(this).attr("data-animation")
                },
                overlay: {
                    color: $(this).attr("data-overlayColor")
                }
            });
            // Open
            modal.open();
        });
    },

    // Counterup
    Components.prototype.initCounterUp = function() {
        var delay = $(this).attr('data-delay')?$(this).attr('data-delay'):100; //default is 100
        var time = $(this).attr('data-time')?$(this).attr('data-time'):1200; //default is 1200
         $('[data-plugin="counterup"]').each(function(idx, obj) {
            $(this).counterUp({
                delay: 100,
                time: 1200
            });
         });
    },

    //peity charts
    Components.prototype.initPeityCharts = function() {
        $('[data-plugin="peity-pie"]').each(function(idx, obj) {
            var colors = $(this).attr('data-colors')?$(this).attr('data-colors').split(","):[];
            var width = $(this).attr('data-width')?$(this).attr('data-width'):20; //default is 20
            var height = $(this).attr('data-height')?$(this).attr('data-height'):20; //default is 20
            $(this).peity("pie", {
                fill: colors,
                width: width,
                height: height
            });
        });
        //donut
         $('[data-plugin="peity-donut"]').each(function(idx, obj) {
            var colors = $(this).attr('data-colors')?$(this).attr('data-colors').split(","):[];
            var width = $(this).attr('data-width')?$(this).attr('data-width'):20; //default is 20
            var height = $(this).attr('data-height')?$(this).attr('data-height'):20; //default is 20
            $(this).peity("donut", {
                fill: colors,
                width: width,
                height: height
            });
        });

        $('[data-plugin="peity-donut-alt"]').each(function(idx, obj) {
            $(this).peity("donut");
        });

        // line
        $('[data-plugin="peity-line"]').each(function(idx, obj) {
            $(this).peity("line", $(this).data());
        });

        // bar
        $('[data-plugin="peity-bar"]').each(function(idx, obj) {
            var colors = $(this).attr('data-colors')?$(this).attr('data-colors').split(","):[];
            var width = $(this).attr('data-width')?$(this).attr('data-width'):20; //default is 20
            var height = $(this).attr('data-height')?$(this).attr('data-height'):20; //default is 20
            $(this).peity("bar", {
                fill: colors,
                width: width,
                height: height
            });
         });
    },

    Components.prototype.initKnob = function() {
        $('[data-plugin="knob"]').each(function(idx, obj) {
           $(this).knob();
        });
    },

    //initilizing
    Components.prototype.init = function () {
        var $this = this;
        this.initTooltipPlugin(),
        this.initPopoverPlugin(),
        this.initToastPlugin(),
        this.initSlimScrollPlugin(),
        this.initFormValidation(),
        this.initCustomModalPlugin(),
        this.initCounterUp(),
        this.initPeityCharts(),
        this.initKnob();
    },

    $.Components = new Components, $.Components.Constructor = Components

}(window.jQuery),


function ($) {
    'use strict';

    var App = function () {
        this.$body = $('body'),
        this.$window = $(window)
    };

    /**
    Resets the scroll
    */
    App.prototype._resetSidebarScroll = function () {
        // sidebar - scroll container
        $('.slimscroll').slimscroll({
            height: 'auto',
            position: 'right',
            size: "4px",
            color: '#83aef2',
            wheelStep: 5
        });
    },

    /**
     * Initlizes the menu - top and sidebar
    */
    App.prototype.initMenu = function () {
        var $this = this;

        // Left menu collapse
        $('.mobile-menu').on('click', function (event) {
            event.preventDefault();
            $this.$body.toggleClass('sidebar-enable');
            if ($this.$window.width() >= 768) {
                $this.$body.toggleClass('enlarged');
            } else {
                $this.$body.removeClass('enlarged');
            }

            // sidebar - scroll container
            $this._resetSidebarScroll();
        });

        // sidebar - main menu
        $("#side-menu").metisMenu();

        // sidebar - scroll container
        $this._resetSidebarScroll();

        // right side-bar toggle
        $('.right-bar-toggle').on('click', function (e) {
            $('body').toggleClass('right-bar-enabled');
        });

        $(document).on('click', 'body', function (e) {
            if ($(e.target).closest('.right-bar-toggle, .right-bar').length > 0) {
                return;
            }

            if ($(e.target).closest('.sidebar, .side-nav').length > 0 || $(e.target).hasClass('mobile-menu')
                || $(e.target).closest('.mobile-menu').length > 0) {
                return;
            }

            $('body').removeClass('right-bar-enabled');
            $('body').removeClass('sidebar-enable');
            return;
        });

        // activate the menu in left side bar based on url
        $("#side-menu a").each(function () {
            var pageUrl = window.location.href.split(/[?#]/)[0];
            if (this.href == pageUrl) {
                $(this).addClass("active");
                $(this).parent().addClass("mm-active"); // add active to li of the current link
                $(this).parent().parent().addClass("mm-show");
                $(this).parent().parent().prev().addClass("active"); // add active class to an anchor
                $(this).parent().parent().parent().addClass("mm-active");
                $(this).parent().parent().parent().parent().addClass("mm-show"); // add active to li of the current link
                $(this).parent().parent().parent().parent().parent().addClass("mm-active");
            }
        });

        // Topbar - main menu
        $('.toggle-button').on('click', function (event) {
            $(this).toggleClass('open');
            $('#navigation').slideToggle(400);
        });

    },

    /**
     * Init the layout - with broad sidebar or compact side bar
    */
    App.prototype.initLayout = function () {
        // in case of small size, add class enlarge to have minimal menu
        if (this.$window.width() >= 768 && this.$window.width() <= 1028) {
            this.$body.addClass('enlarged');
        } else {
            if (this.$body.data('keep-enlarged') != true) {
                this.$body.removeClass('enlarged');
            }
        }
    },

    //initilizing
    App.prototype.init = function () {
        var $this = this;
        this.initLayout();
        this.initMenu();
        $.Components.init();
        // on window resize, make menu flipped automatically
        $this.$window.on('resize', function (e) {
            e.preventDefault();
            $this.initLayout();
            $this._resetSidebarScroll();
        });
    },

    $.App = new App, $.App.Constructor = App


}(window.jQuery),
//initializing main application module
function ($) {
    "use strict";
    $.App.init();
}(window.jQuery);

// Waves Effect
Waves.init();