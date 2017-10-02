+function ($) {
  'use strict';

  // MODAL CLASS DEFINITION
  // ======================

  var Sidemodal = function (element, options) {
    this.options             = options
    this.$body               = $(document.body)
    this.$element            = $(element)
    this.$dialog             = this.$element.find('.sidemodal-dialog')
    this.$backdrop           = null
    this.isShown             = null
    this.originalBodyPad     = null
    this.scrollbarWidth      = 0
    this.ignoreBackdropClick = false

    if (this.options.remote) {
      this.$element
        .find('.sidemodal-content')
        .load(this.options.remote, $.proxy(function () {
          this.$element.trigger('loaded.bs.sidemodal')
        }, this))
    }
  }

  Sidemodal.VERSION  = '3.3.7'

  Sidemodal.TRANSITION_DURATION = 300
  Sidemodal.BACKDROP_TRANSITION_DURATION = 150

  Sidemodal.DEFAULTS = {
    backdrop: true,
    keyboard: true,
    show: true
  }

  Sidemodal.prototype.toggle = function (_relatedTarget) {
    return this.isShown ? this.hide() : this.show(_relatedTarget)
  }

  Sidemodal.prototype.show = function (_relatedTarget) {
    var that = this
    var e    = $.Event('show.bs.sidemodal', { relatedTarget: _relatedTarget })

    this.$element.trigger(e)

    if (this.isShown || e.isDefaultPrevented()) return

    this.isShown = true

    this.checkScrollbar()
    this.setScrollbar()
    this.$body.addClass('sidemodal-open')

    this.escape()
    this.resize()

    this.$element.on('click.dismiss.bs.sidemodal', '[data-dismiss="sidemodal"]', $.proxy(this.hide, this))

    this.$dialog.on('mousedown.dismiss.bs.sidemodal', function () {
      that.$element.one('mouseup.dismiss.bs.sidemodal', function (e) {
        if ($(e.target).is(that.$element)) that.ignoreBackdropClick = true
      })
    })

    this.backdrop(function () {
      var transition = $.support.transition && that.$element.hasClass('fade')

      if (!that.$element.parent().length) {
        that.$element.appendTo(that.$body) // don't move modals dom position
      }

      that.$element
        .show()
        .scrollTop(0)

      that.adjustDialog()

      if (transition) {
        that.$element[0].offsetWidth // force reflow
      }

      that.$element.addClass('in')

      that.enforceFocus()

      var e = $.Event('shown.bs.sidemodal', { relatedTarget: _relatedTarget })

      transition ?
        that.$dialog // wait for modal to slide in
          .one('bsTransitionEnd', function () {
            that.$element.trigger('focus').trigger(e)
          })
          .emulateTransitionEnd(Sidemodal.TRANSITION_DURATION) :
        that.$element.trigger('focus').trigger(e)
    })
  }

  Sidemodal.prototype.hide = function (e) {
    if (e) e.preventDefault()

    e = $.Event('hide.bs.sidemodal')

    this.$element.trigger(e)

    if (!this.isShown || e.isDefaultPrevented()) return

    this.isShown = false

    this.escape()
    this.resize()

    $(document).off('focusin.bs.sidemodal')

    this.$element
      .removeClass('in')
      .off('click.dismiss.bs.sidemodal')
      .off('mouseup.dismiss.bs.sidemodal')

    this.$dialog.off('mousedown.dismiss.bs.sidemodal')

    $.support.transition && this.$element.hasClass('fade') ?
      this.$element
        .one('bsTransitionEnd', $.proxy(this.hideSidemodal, this))
        .emulateTransitionEnd(Sidemodal.TRANSITION_DURATION) :
      this.hideSidemodal()
  }

  Sidemodal.prototype.enforceFocus = function () {
    $(document)
      .off('focusin.bs.sidemodal') // guard against infinite focus loop
      .on('focusin.bs.sidemodal', $.proxy(function (e) {
        if (document !== e.target &&
            this.$element[0] !== e.target &&
            !this.$element.has(e.target).length) {
          this.$element.trigger('focus')
        }
      }, this))
  }

  Sidemodal.prototype.escape = function () {
    if (this.isShown && this.options.keyboard) {
      this.$element.on('keydown.dismiss.bs.sidemodal', $.proxy(function (e) {
        e.which == 27 && this.hide()
      }, this))
    } else if (!this.isShown) {
      this.$element.off('keydown.dismiss.bs.sidemodal')
    }
  }

  Sidemodal.prototype.resize = function () {
    if (this.isShown) {
      $(window).on('resize.bs.sidemodal', $.proxy(this.handleUpdate, this))
    } else {
      $(window).off('resize.bs.sidemodal')
    }
  }

  Sidemodal.prototype.hideSidemodal = function () {
    var that = this
    this.$element.hide()
    this.backdrop(function () {
      that.$body.removeClass('sidemodal-open')
      that.resetAdjustments()
      that.resetScrollbar()
      that.$element.trigger('hidden.bs.sidemodal')
    })
  }

  Sidemodal.prototype.removeBackdrop = function () {
    this.$backdrop && this.$backdrop.remove()
    this.$backdrop = null
  }

  Sidemodal.prototype.backdrop = function (callback) {
    var that = this
    var animate = this.$element.hasClass('fade') ? 'fade' : ''

    if (this.isShown && this.options.backdrop) {
      var doAnimate = $.support.transition && animate

      this.$backdrop = $(document.createElement('div'))
        .addClass('sidemodal-backdrop ' + animate)
        .appendTo(this.$body)

      this.$element.on('click.dismiss.bs.sidemodal', $.proxy(function (e) {
        if (this.ignoreBackdropClick) {
          this.ignoreBackdropClick = false
          return
        }
        if (e.target !== e.currentTarget) return
        this.options.backdrop == 'static'
          ? this.$element[0].focus()
          : this.hide()
      }, this))

      if (doAnimate) this.$backdrop[0].offsetWidth // force reflow

      this.$backdrop.addClass('in')

      if (!callback) return

      doAnimate ?
        this.$backdrop
          .one('bsTransitionEnd', callback)
          .emulateTransitionEnd(Sidemodal.BACKDROP_TRANSITION_DURATION) :
        callback()

    } else if (!this.isShown && this.$backdrop) {
      this.$backdrop.removeClass('in')

      var callbackRemove = function () {
        that.removeBackdrop()
        callback && callback()
      }
      $.support.transition && this.$element.hasClass('fade') ?
        this.$backdrop
          .one('bsTransitionEnd', callbackRemove)
          .emulateTransitionEnd(Sidemodal.BACKDROP_TRANSITION_DURATION) :
        callbackRemove()

    } else if (callback) {
      callback()
    }
  }

  // these following methods are used to handle overflowing modals

  Sidemodal.prototype.handleUpdate = function () {
    this.adjustDialog()
  }

  Sidemodal.prototype.adjustDialog = function () {
    var modalIsOverflowing = this.$element[0].scrollHeight > document.documentElement.clientHeight

    this.$element.css({
      paddingLeft:  !this.bodyIsOverflowing && modalIsOverflowing ? this.scrollbarWidth : '',
      paddingRight: this.bodyIsOverflowing && !modalIsOverflowing ? this.scrollbarWidth : ''
    })
  }

  Sidemodal.prototype.resetAdjustments = function () {
    this.$element.css({
      paddingLeft: '',
      paddingRight: ''
    })
  }

  Sidemodal.prototype.checkScrollbar = function () {
    var fullWindowWidth = window.innerWidth
    if (!fullWindowWidth) { // workaround for missing window.innerWidth in IE8
      var documentElementRect = document.documentElement.getBoundingClientRect()
      fullWindowWidth = documentElementRect.right - Math.abs(documentElementRect.left)
    }
    this.bodyIsOverflowing = document.body.clientWidth < fullWindowWidth
    this.scrollbarWidth = this.measureScrollbar()
  }

  Sidemodal.prototype.setScrollbar = function () {
    var bodyPad = parseInt((this.$body.css('padding-right') || 0), 10)
    this.originalBodyPad = document.body.style.paddingRight || ''
    if (this.bodyIsOverflowing) this.$body.css('padding-right', bodyPad + this.scrollbarWidth)
  }

  Sidemodal.prototype.resetScrollbar = function () {
    this.$body.css('padding-right', this.originalBodyPad)
  }

  Sidemodal.prototype.measureScrollbar = function () { // thx walsh
    var scrollDiv = document.createElement('div')
    scrollDiv.className = 'sidemodal-scrollbar-measure'
    this.$body.append(scrollDiv)
    var scrollbarWidth = scrollDiv.offsetWidth - scrollDiv.clientWidth
    this.$body[0].removeChild(scrollDiv)
    return scrollbarWidth
  }


  // MODAL PLUGIN DEFINITION
  // =======================

  function Plugin(option, _relatedTarget) {
    return this.each(function () {
      var $this   = $(this)
      var data    = $this.data('bs.sidemodal')
      var options = $.extend({}, Sidemodal.DEFAULTS, $this.data(), typeof option == 'object' && option)

      if (!data) $this.data('bs.sidemodal', (data = new Sidemodal(this, options)))
      if (typeof option == 'string') data[option](_relatedTarget)
      else if (options.show) data.show(_relatedTarget)
    })
  }

  var old = $.fn.sidemodal

  $.fn.sidemodal             = Plugin
  $.fn.sidemodal.Constructor = Sidemodal


  // MODAL NO CONFLICT
  // =================

  $.fn.sidemodal.noConflict = function () {
    $.fn.sidemodal = old
    return this
  }


  // MODAL DATA-API
  // ==============

  $(document).on('click.bs.sidemodal.data-api', '[data-toggle="sidemodal"]', function (e) {
    var $this   = $(this)
    var href    = $this.attr('href')
    var $target = $($this.attr('data-target') || (href && href.replace(/.*(?=#[^\s]+$)/, ''))) // strip for ie7
    var option  = $target.data('bs.sidemodal') ? 'toggle' : $.extend({ remote: !/#/.test(href) && href }, $target.data(), $this.data())

    if ($this.is('a')) e.preventDefault()

    $target.one('show.bs.sidemodal', function (showEvent) {
      if (showEvent.isDefaultPrevented()) return // only register focus restorer if modal will actually get shown
      $target.one('hidden.bs.sidemodal', function () {
        $this.is(':visible') && $this.trigger('focus')
      })
    })
    Plugin.call($target, option, this)
  })

}(jQuery);