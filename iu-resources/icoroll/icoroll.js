var IcoRoll = function (e, t) {
    var n = this;
    this._def_menu_attrs = {
        position: "left",
        margin: 0,
        elements: []
    };
    this._menu = {};
    for (attr in this._def_menu_attrs) this._menu[attr] = this._def_menu_attrs[attr];
    for (attr in t) this._menu[attr] = t[attr];
    this._tip_free = true;
    this._attrs = {};
    this._back = false;
    this._ctime;
    this._is_scrolling = false;
    this._should_hide_back = false;
    this._menu_showed = false;
    this._can_add_hide_button = true;
    this._first_time = true;
    this._istouching = false;
    this._current_outside = false;
    this._hovered_menu = false;
    this._is_hovered = false;
    this._above_size = 0;
    this._under_size = 0;
    this._default_attrs = {
        links: $iu$(".icoroll"),
        time: 2e3,
        type: "scroll",
        tip_distance: 30,
        tip_text: "Click to see more",
        back_enabled: true,
        back_text: "Back",
        mobile_width: 600,
        menu_show_time: 500,
        onmenuClick: function (e, t) {},
        onmenuCurrent: function (e, t) {}
    };
    for (attr in this._default_attrs) this._attrs[attr] = this._default_attrs[attr];
    for (attr in e) this._attrs[attr] = e[attr];
    var r = this._attrs.links;
    this._time = this._attrs.time;
    this._init = function () {
        this._window_height = $iu$(window).height();
        r.css("cursor", "pointer");
        $iu$("body").append('<div id="scroll_tip" class="scroll_colors"></div>');
        $iu$("body").append('<div id="scroll_back" ><span class="scroll_colors">' + this._attrs.back_text + "</span></div>");
        this._$ = $iu$("#scroll_tip");
        this._$back = $iu$("#scroll_back");
        this._hideBack();
        var e = this._attrs["menu_show_time"] / 1e3;
        e = "top " + e + "s";
        this._$back.css({
            transition: e,
            "-moz-transition": e,
            "-webkit-transition": e,
            "-o-transition": e
        });
        if (!this._attrs.back_enabled) this._$back.css({
            display: "none"
        });
        if (this._menu) {
            this._generateMenu();
            this._generateBlocks()
        }
        if (this._attrs.type == "scroll") {
            this._bind();
            $iu$(window).trigger("scroll");
            $iu$(window).trigger("resize");
            $iu$(window).load(function () {
                n._generateBlocks();
                $iu$(window).trigger("scroll")
            })
        } else {
            this._bindStandard();
            $iu$(window).trigger("resize")
        } if (this._menu) {
            this._generateBlocks();
            this._setMenuTransition()
        }
    };
    this._generateMenu = function () {
        var e = '<div id="scroll_menu"><div id="scroll_show_menu_button" class="icon-menu-2 scroll_colors hide_button"></div>';
        e += '<div id="scroll_up_button" class="hover_colors icon-arrow-up nav" ></div>';
        e += '<ul class="scroll_menu">';
        for (el in this._menu.elements) {
            e += "<li ";
            if (this._menu.margin) e += 'style=" margin:' + this._menu.margin + ' 0px"';
            e += 'id="menu_scroll_number_' + el + '" data-number="' + el + '">';
            if (this._menu.position && this._menu.position == "right") {
                e += '<a href="' + this._menu.elements[el].href + '" class="scroll_colors ' + this._menu.elements[el]["class"] + '"></a>';
                if (this._menu.elements[el].content) e += '<span class="scroll_colors">' + this._menu.elements[el].content + "</span>"
            } else {
                if (this._menu.elements[el].content) e += '<span class="scroll_colors">' + this._menu.elements[el].content + "</span>";
                e += '<a href="' + this._menu.elements[el].href + '" class="scroll_colors ' + this._menu.elements[el]["class"] + '"></a>'
            }
            e += "</li>"
        }
        e += "</ul>";
        e += '<div id="scroll_down_button" class="hover_colors icon-arrow-down nav" ></div></div>';
        $iu$("body").append(e);
        this._$menu = $iu$(".scroll_menu");
        if (this._menu.position && this._menu.position == "right") $iu$(".scroll_menu li span").css({
            left: this._$menu.width()
        });
        else $iu$(".scroll_menu li span").css({
            right: this._$menu.width()
        }); if (this._menu.position && this._menu.position == "right") this._$menu.addClass("scroll_right_menu");
        else this._$menu.addClass("scroll_left_menu");
        this._$button = $iu$("#scroll_show_menu_button");
        this._$nav_up = $iu$("#scroll_up_button");
        this._$nav_down = $iu$("#scroll_down_button");
        this._$navs = $iu$("#scroll_menu .nav");
        this._setMenuPosition();
        this._setMenuButtons();
        this._countHeights();
        this._setMenuPosition()
    };
    this._setMenuTransition = function () {
        var e = this._attrs["menu_show_time"] / 1e3;
        var t = "opacity " + e + "s, right " + e + "s, left " + e + "s";
        var n = "top 0.4s," + t;
        this._$menu.css({
            transition: n,
            "-moz-transition": n,
            "-webkit-transition": n,
            "-o-transition": n
        });
        this._$navs.css({
            transition: t,
            "-moz-transition": t,
            "-webkit-transition": t,
            "-o-transition": t
        })
    };
    this._setMenuButtons = function () {
        if (this._menu.position && this._menu.position == "right") {
            this._$button.addClass("right");
            this._$navs.addClass("right")
        } else {
            this._$button.addClass("left");
            this._$navs.addClass("left")
        }
    };
    this._setMenuPosition = function () {
        this._$menu.css("top", this._above_size);
        this._$menu.css("display", "block");
        this._mousestep = this._$menu.find("li").height();
        this._menu_height = this._$menu.height()
    };
    this._countHeights = function () {
        if (this._menu_height < n._window_height - this._above_size - this._under_size) this._above_size = (n._window_height - this._menu_height) / 2;
        else this._above_size += this._$nav_up.height();
        this._under_size = this._$nav_down.height();
        this._menu_visible_elements = Math.round((n._window_height - this._above_size - this._under_size) / this._$menu.find("li").height());
        this._menu_center_element = Math.ceil(this._menu_visible_elements / 2)
    };
    this._generateBlocks = function () {
        if (this._attrs.type == "standard") return;
        this._blocks = [];
        var e = n.__checkBrowser();
        for (i in this._menu.elements) {
            var t = this._menu.elements[i].href;
            if (t.match(/^http/)) continue;
            if ($iu$(this._menu.elements[i].href).length > 0) this._blocks[$iu$(t).offset().top] = t
        }
    };
    this._linkScrollEvent = function (e) {
        this._back = e.offset().top - n._window_height / 2;
        var t = e.attr("data-href");
        var r = e.attr("data-back") ? e.attr("data-back") : "";
        this._$back.find("span").html(this._attrs.back_text + " " + r);
        if (!t) t = e.attr("href");
        this._scroll(e, $iu$(t));
        if (e.attr("data-noback") == null) {
            this._showBack()
        }
    };
    this._scroll = function (e, t, n) {
        var r = this;
        this._is_scrolling = true;
        this._should_hide_back = false;
        var i = typeof e == "number" ? e : e.offset().top;
        var s = typeof t == "number" ? t : t.offset().top;
        var o = $iu$("body").height();
        var u = Math.abs(i - s) / o * this._time;
        if (!r.__isTouchIe()) {
            r._$back.css("display", "none");
            $iu$("html,body").animate({
                scrollTop: s - this._$back.height() / 2
            }, u, function () {
                r._is_scrolling = false;
                r._$back.css("display", "block");
                if (n) n()
            })
        } else $iu$("html,body").scrollTop(s - this._$back.height() / 2);
        this._ctime = u
    };
    this._bind = function () {
        this._bindLinks();
        this._bindScroll();
        this._bindBack();
        if (this._menu) {
            this._bindMenu();
            this._bindNavs();
            this._bindBrowserLocation();
            this._bindBrowserBack()
        }
        this._bindResize();
        this._bindShowButton()
    };
    this._bindStandard = function () {
        if (this._menu) {
            this._bindMenu();
            this._bindNavs();
            var e = false;
            for (el in this._menu.elements) {
                if (this._menu.elements[el].current) {
                    e = n._$menu.find('a[href="' + this._menu.elements[el].href + '"]');
                    if (e.length == 0) e = n._$menu.find('a[href*="' + this._menu.elements[el].href + '"]');
                    break
                }
                if (this._menu.elements[el].href[0] != "/") var t = "/" + this._menu.elements[el].href;
                else var t = this._menu.elements[el].href; if (window.location.href.match(t)) e = n._$menu.find('a[href*="' + this._menu.elements[el].href + '"]')
            }
            if (e == false) e = n._$menu.find('a[href*="' + this._menu.elements[0].href + '"]');
            this._checkMenu(e.parent());
            this._current = e
        }
        this._bindResize();
        this._bindShowButton()
    };
    this._bindLinks = function () {
        var e = this;
        r.bind("click", function (t) {
            e._linkScrollEvent($iu$(this));
            t.preventDefault();
            return false
        });
        r.hover(function () {
            var t = $iu$(this).attr("data-title") ? $iu$(this).attr("data-title") : e._attrs.tip_text;
            e._$.html("<span>" + t + "</span>");
            if (e._attrs.tip_distance) var n = e._attrs.tip_distance;
            else var n = $iu$(this).height() > e._$.height() ? $iu$(this).height() : e._$.height();
            var r = $iu$(this).offset().top - n;
            var i = $iu$(this).offset().left + $iu$(this).width() / 2 - e._$.width() / 2;
            e._$.css({
                top: r,
                left: i
            });
            e._$.addClass("scroll_tip_show")
        }, function () {
            e._$.removeClass("scroll_tip_show")
        })
    };
    this._bindBack = function () {
        var e = this;
        this._$back.bind("click", function (t) {
            if (e._ctime == null || e._ctime == 0) e._ctime = 200;
            if (e._back) {
                $iu$("html,body").animate({
                    scrollTop: e._back
                }, e._ctime);
                e._hideBack()
            }
            t.preventDefault();
            t.stopPropagation();
            return false
        })
    };
    this._bindResize = function () {
        var e = this;
        $iu$(window).bind("resize", function () {
            if (!e._menu) return;
            e._window_height = $iu$(window).height();
            e._generateBlocks();
            e._above_size = 0;
            e._under_size = 0;
            if ($iu$(window).width() < e._attrs.mobile_width) {
                if (e._first_time) {
                    e._hideMenu();
                    e._first_time = false
                }
                e._showButton();
                e._above_size = e._$button.height()
            } else {
                e._showMenu();
                e._hideButton()
            } if (e._window_height - e._above_size < e._menu_height) {
                e._showUpDownButtons();
                e._countHeights()
            } else {
                e._above_size = (e._window_height - e._menu_height) / 2;
                e._hideUpDownButtons()
            }
            e._setMenuPosition()
        })
    };
    this._bindBrowserBack = function () {
        $iu$(window).on("popstate", function (e) {
            e.stopPropagation();
            e.preventDefault();
            return false
        })
    };
    this._bindBrowserLocation = function () {
        $iu$(window).on("hashchange", function (e) {
            var t = window.location.href.substr(window.location.href.indexOf("#"));
            var r = n._$menu.find('a[href="' + t + '"]').first().parent();
            r.trigger("click");
            e.stopPropagation();
            e.preventDefault();
            return false
        })
    };
    this.__menuUp = function (e) {
        var t = parseInt(n._$menu.css("top"));
        if (t + n._mousestep > n._above_size) n._$menu.css("top", n._above_size);
        else n._$menu.css("top", t + n._mousestep);
        e.preventDefault();
        return false
    };
    this.__menuDown = function (e) {
        var t = parseInt(n._$menu.css("top"));
        var r = n._window_height - n._under_size;
        if (t + n._menu_height - n._mousestep < r) n._$menu.css("top", r - n._menu_height);
        else n._$menu.css("top", t - n._mousestep);
        e.preventDefault();
        return false
    };
    this._bindNavs = function () {
        var e = this;
        this._$nav_up.click(this.__menuUp);
        this._$nav_down.click(this.__menuDown)
    };
    this._bindShowButton = function () {
        var e = this;
        $iu$(document).on("click", ".hide_button", function () {
            e.__buttonShowHideMenuEvent()
        })
    };
    this.__buttonShowHideMenuEvent = function () {
        if (!this._menu_showed) {
            this._showMenu();
            this._menu_showed = true
        } else {
            this._hideMenu();
            this._menu_showed = false
        }
    };
    this._bindMenu = function () {
        var e = this;
        var t = function (t) {
            if (!t) var t = this;
            if (e._istouching) return false;
            e._hideBack();
            var n = e._current.offset().top;
            if (n < e._$nav_up.offset().top || n > e._$nav_down.offset().top) {
                e._current_outside = true
            } else e._current_outside = false;
            var r = $iu$(t).find("a").attr("href");
            var i = $iu$(t);
            i.siblings().removeClass("current");
            if (e._menu.elements[i.attr("data-number")].onClick != null) e._menu.elements[i.attr("data-number")].onClick(i, parseInt(i.attr("data-number")));
            else e._attrs.onmenuClick(i, parseInt(i.attr("data-number")));
            var s = e.__checkBrowser();
            if (r.match(/^http/)) {
                if (s.browser == "msie" && parseInt(s.version) < 8) {
                    var o = r.match(/#([a-z0-9_]+)/);
                    if (o) r = o[0];
                    else return window.open(r)
                } else return window.open(r)
            }
            if (e._attrs.type == "standard") return window.location.assign(r);
            if ($iu$(r).length == 0) return;
            e._scroll($iu$(window).scrollTop(), $iu$(r), function () {
                e._current_outside = false;
                e._saveToHistory(i)
            });
            return false
        };
        var n = function () {
            var t = $iu$(this);
            var n = $iu$(this).find("a");
            var r = $iu$(this).find("span");
            if (e._hovered_menu) t.siblings().css("left", 0);
            if (e.__isTouchBrowser()) t.siblings().removeClass("current");
            var i = e.__checkBrowser();
            if (i.browser == "msie" && parseInt(i.version) < 8) return false;
            e._hovered_menu = t;
            e._is_hovered = true;
            if (e._menu.position && e._menu.position == "right") t.css("left", -(parseInt(r.width()) + parseInt(r.css("padding-right"))));
            else t.css("left", parseInt(r.width()) + parseInt(r.css("padding-left")))
        };
        var r = function () {
            $iu$(this).css("left", 0)
        };
        this.menuHover = n;
        this.menuUnhover = r;
        this._$menu.find("li").hover(function (e) {
            n.call(this)
        }, function (e) {
            r.call(this)
        });
        this._$menu.find("a").click(function (e) {
            e.preventDefault();
            t($iu$(this).parent().get());
            return false
        });
        this._$menu.find("li").click(function (e) {
            t(this)
        });
        var i = this._$menu.find("li").get();
        if (i[0]["addEventListener"])
            for (el in i) {
                i[el].addEventListener("touchstart", function (t) {
                    t.stopPropagation();
                    t.preventDefault();
                    e._mousey = t.touches[0].screenY;
                    e._mousestep = e._$menu.find("a").height();
                    e._touchtime = (new Date).getTime();
                    e._istouching = true
                }, false);
                i[el].addEventListener("touchend", function (i) {
                    i.stopPropagation();
                    i.preventDefault();
                    e._istouching = false;
                    var s = (new Date).getTime();
                    if (Math.abs(s - e._touchtime) < 200) {
                        if (e._current != null) r.call(e._current);
                        t(this);
                        if (!$iu$(this).hasClass("current") || $iu$(this).hasClass("hided")) {
                            n.call($iu$(this));
                            $iu$(this).removeClass("hided")
                        } else $iu$(this).addClass("hided")
                    } else {
                        r.call(e._current)
                    }
                }, false);
                i[el].addEventListener("touchmove", function (t) {
                    t.stopPropagation();
                    t.preventDefault();
                    var n = e._mousey - t.touches[0].screenY;
                    n = -n;
                    if (e._window_height > e._$menu.height() + e._$nav_up.height() * 2) return;
                    if (Math.abs(n) < e._mousestep) n = n > 0 ? e._mousestep : -e._mousestep;
                    n = n * 2;
                    nvalue = parseInt(e._$menu.css("top")) + n;
                    var i = nvalue + parseInt(e._menu_height);
                    var s = nvalue - e._above_size;
                    if (i <= e._window_height) return e._$menu.css("top", e._window_height - e._menu_height - e._under_size);
                    if (s >= 0) return e._$menu.css("top", e._above_size);
                    e._$menu.css("top", nvalue);
                    e._mousey = t.touches[0].screenY;
                    r(e._current)
                }, false)
            }
    };
    this._bindScroll = function () {
        var e = this;
        var t = function (t) {
            var n = $iu$(window).scrollTop();
            var r = 99999999;
            var s;
            for (i in e._blocks) {
                if (Math.abs(n - i) < r) {
                    r = parseInt(Math.abs(n - i));
                    s = e._blocks[i]
                }
            }
            if (e._menu) {
                var o = e._$menu.find('a[href="' + s + '"]');
                if (o.length == 0) o = e._$menu.find('a[href*="' + s + '"]');
                if (e._is_hovered && o.parent().attr("data-number") == e._hovered_menu.attr("data-number")) e._is_hovered = false;
                else if (!e._is_hovered && e._hovered_menu && o.parent().attr("data-number") != e._hovered_menu.attr("data-number")) {
                    e._hovered_menu.css("left", 0);
                    e._hovered_menu = false
                }
                e._checkMenu(o.parent())
            }
            if (e._is_scrolling) return;
            if (!e._should_hide_back) return e._should_hide_back = true;
            e._should_hide_back = false;
            e._hideBack()
        };
        var n = function () {
            e._$back.css("display", "block");
            t()
        };
        var r = function () {
            e._$back.css("display", "none");
            t()
        };
        $iu$(window).on("scroll", t);
        document.addEventListener("touchmove", r, false);
        if (e._menu) this._$menu.on("mousewheel", function (t) {
            if (e._menu_height < e._window_height - e._above_size - e._under_size) return false;
            if (t.originalEvent.wheelDelta / 120 > 0) {
                e.__menuUp(t)
            } else {
                e.__menuDown(t)
            }
        })
    };
    this._saveToHistory = function (e) {
        if (!window.history.state || window.history.state.url != e.find("a").attr("href"))
            if (window.history["pushState"]) window.history.pushState({
                url: e.find("a").attr("href")
            }, e.find("a").attr("href"), e.find("a").attr("href"))
    };
    this._unbindScroll = function () {
        $iu$(window).unbind("scroll")
    };
    this._hideMenu = function () {
        if (this._menu.position && this._menu.position == "right") {
            this._$navs.css("right", -this._$menu.width());
            this._$menu.css("right", -this._$menu.width())
        } else {
            this._$navs.css("left", -this._$menu.width());
            this._$menu.css("left", -this._$menu.width())
        }
        this._current.css("left", 0);
        this._changeToShowButton()
    };
    this._showMenu = function () {
        if (this._menu.position && this._menu.position == "right") {
            this._$navs.css("right", 0);
            this._$menu.css("right", 0)
        } else {
            this._$navs.css("left", 0);
            this._$menu.css("left", 0)
        }
        this._changeToHideButton()
    };
    this._showUpDownButtons = function () {
        this._$navs.css("display", "block");
        this._$nav_up.css({
            top: this._above_size,
            width: this._$menu.find("a").width()
        });
        this._$nav_down.css({
            bottom: 0,
            width: this._$menu.find("a").width()
        })
    };
    this._hideUpDownButtons = function () {
        this._$navs.css("display", "none")
    };
    this._showButton = function () {
        this._$button.css("display", "block")
    };
    this._hideButton = function () {
        this._$button.css("display", "none")
    };
    this._hideBack = function () {
        this._$back.css("top", -this._$back.height() - 1)
    };
    this._showBack = function () {
        this._$back.css("top", 0)
    };
    this._changeToHideButton = function () {
        this._$button.removeClass("icon-menu-2");
        this._$button.addClass("icon-close")
    };
    this._changeToShowButton = function () {
        this._$button.removeClass("icon-close");
        this._$button.addClass("icon-menu-2")
    };
    this._checkMenu = function (e) {
        e.siblings().removeClass("current");
        e.addClass("current");
        if (this._attrs.type == "standard") return;
        if (this._menu_timeout_id != null) window.clearTimeout(this._menu_timeout_id);
        this._menu_timeout_id = window.setTimeout(function () {
            n._saveToHistory(e)
        }, 3e3);
        var t = false;
        if (!this._current || this._current.attr("data-number") != e.attr("data-number")) {
            if (n._menu.elements[e.attr("data-number")].onCurrent != null) n._menu.elements[e.attr("data-number")].onCurrent(e, parseInt(e.attr("data-number")));
            else n._attrs.onmenuCurrent(e, parseInt(e.attr("data-number")))
        }
        this._current = e;
        if (this._menu_height < n._window_height - n._above_size - n._under_size || this._current_outside) return;
        var r = e.offset().top;
        var i = this._$menu.offset().top;
        var s = Math.round((r - i) / this._mousestep) + 1;
        if (s < n._menu_center_element) var o = $iu$("#menu_scroll_number_0");
        else var o = $iu$("#menu_scroll_number_" + parseInt(s - n._menu_center_element + 1)); if (s > this._menu.elements.length - (n._menu_visible_elements - n._menu_center_element)) var u = $iu$("#menu_scroll_number_" + (this._menu.elements.length - 1));
        else var u = $iu$("#menu_scroll_number_" + parseInt(s + (n._menu_visible_elements - n._menu_center_element) - 1));
        var a = $iu$(window).scrollTop();
        var f = o.offset().top - a;
        var l = u.offset().top - a;
        var c = n._window_height - this._under_size;
        var h = this._above_size;
        var p = this._$menu.offset().top - a;
        if (l > c) {
            var d = true;
            t = n._window_height - this._under_size - (l - p) - this._mousestep
        } else if (f < h) {
            var d = false;
            t = this._above_size - (f - p)
        }
        if (t === false) return;
        t = Math.round(t / this._mousestep) * this._mousestep;
        var v = t + parseInt(this._menu_height);
        var p = t - this._above_size;
        if (v <= n._window_height) return n._$menu.css("top", n._window_height - n._menu_height - n._under_size);
        if (p >= 0) return this._$menu.css("top", this._above_size);
        this._$menu.css("top", t)
    };
    this.__isTouchBrowser = function () {
        return "ontouchstart" in window || navigator.msMaxTouchPoints > 0
    };
    this.__isTouchIe = function () {
        return ("ontouchstart" in window || navigator.msMaxTouchPoints > 0) && this.__checkBrowser().browser == "msie"
    };
    this.__checkBrowser = function () {
        uaMatch = function (e) {
            e = e.toLowerCase();
            var t = /(chrome)[ \/]([\w.]+)/.exec(e) || /(webkit)[ \/]([\w.]+)/.exec(e) || /(opera)(?:.*version|)[ \/]([\w.]+)/.exec(e) || /(msie) ([\w.]+)/.exec(e) || e.indexOf("compatible") < 0 && /(mozilla)(?:.*? rv:([\w.]+)|)/.exec(e) || [];
            return {
                browser: t[1] || "",
                version: t[2] || "0"
            }
        };
        return uaMatch(navigator.userAgent)
    };
    this._init()
};
icoroll = function (e, t) {
    return new IcoRoll(e, t)
}