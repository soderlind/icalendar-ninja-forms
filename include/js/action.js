/**
 * Add date and time picker to iCalendar action setting.
 *
 * @author Per Soderlind http://soderlind.no
 *
 */
document.addEventListener("DOMContentLoaded", () => {
  const nfRadio = Backbone.Radio; // rome-ignore lint/js/noUndeclaredVariables: Backbone is an external object.

  const iCalendarDateSettingChannel = nfRadio.channel("setting-icalendar_date");
  const iCalendarEndDateSettingChannel = nfRadio.channel(
    "setting-icalendar_end_date"
  );

  const iCalendarsSettings = class extends Marionette.Object {
    // rome-ignore lint/js/noUndeclaredVariables: Marionett is an external object.
    nfTextboxStyle = {
      background: "#f9f9f9",
      border: "0",
      marginTop: "7px",
      padding: "12px 15px",
      width: "100%",
      height: "41px",
    };
    min_val = 0;
    /**
     * initialize()
     *
     */
    initialize() {
      this.listenTo(
        iCalendarDateSettingChannel,
        "render:setting",
        this.renderDateField
      );
      this.listenTo(
        nfRadio.channel("app"),
        "change:setting",
        this.onDateChange,
        this
      );

      this.listenTo(
        iCalendarEndDateSettingChannel,
        "render:setting",
        this.renderDateField
      );
    }

    /**
     * Convert the textbox (input type="text") to date field (input type="datetime-local").
     *
     * - If empty, set the date to "today", in the format YYYY-MM-DD.
     * - Set the style of the field to the same as a textbox.
     *
     * @see https://caniuse.com/input-datetime
     *
     * @param {*} settingModel
     * @param {*} dataModel
     * @param {*} view
     */
    renderDateField(settingModel, dataModel, view) {
      const element = view.el.getElementsByClassName("setting")[0];
      let value = 0;
      element.attributes["type"].value = "datetime-local";

      if (!element.value) {
        value = new Date();
        this.setDate(element, value);
        this.setDateMinAttribute(element, value);
      } else {
        value = element.value;
        this.setDate(element, value);
        if (this.min_val !== 0) {
          this.setDateMinAttribute(element, this.min_val);
        }
      }
      if (settingModel.get("name") == "icalendar_date") {
        this.min_val = value;
      }
      Object.assign(element.style, this.nfTextboxStyle);
    }

    /**
     * When we change the start date, update the end date.
     *
     * @author Per Søderlind
     * @param {object} e
     * @param {backbone.model} settingModel
     * @param {backbone.model} dataModel
     * @param {string} v
     */
    onDateChange(e, settingModel, dataModel, v) {
      const name = settingModel.get("name");
      const self = e.currentTarget;

      if (name === "icalendar_date") {
        const value = this.keepDateInRange(self);
        const end_date_field = document.getElementById("icalendar_end_date");
        this.setEndDate(end_date_field, value);
        this.setDateMinAttribute(end_date_field, value);
      }

      if (name === "icalendar_end_date") {
        const value = this.keepDateInRange(self);
      }
    }
    /**
     * Keep the date in range. If the date is before "now", set it to "now".
     *
     * @author Per Søderlind
     * @param {*} self
     * @returns {*}
     */
    keepDateInRange(self) {
      let value = self.value;
      const min_val = self.getAttribute("min");
      if (!self.validity.valid || !this.isValidDate(value)) {
        value = min_val;
        self.value = min_val;
        self.setCustomValidity("");
      }
      return value;
    }

    /**
     * Set the end date.
     *
     * @author Per Søderlind
     * @param {*} value
     */
    setEndDate(self, value) {
      if (this.isValidDate(value)) {
        this.setDate(self, value);
      }
    }

    /**
     * Get the end date.
     *
     * @author Per Søderlind
     * @returns {*}
     */
    getDateFromField(element) {
      const date = element.value;
      return this.isValidDate(date) ? date : 0;
    }

    /**
     * Check if the date is valid.
     *
     * @author Per Søderlind
     * @param {*} date
     * @returns {*}  {boolean}
     */
    isValidDate(date) {
      return !isNaN(Date.parse(date));
    }

    /**
     * Set the date.
     *
     * @author Per Søderlind
     * @param {*} element
     * @param {*} value
     */
    setDate(element, value) {
      const date = new Date(value);

      element.value = this.roundedISODate(date);
    }
    /**
     * Set min attribute to the date field. This is set to "now".
     *
     * @author Per Søderlind
     * @param {*} element
     */
    setDateMinAttribute(element, date) {
      if (date !== null && this.isValidDate(date)) {
        date = new Date(date);
      } else {
        date = new Date();
      }

      element.setAttribute("min", this.roundedISODate(date));
    }

    /**
     * Round the iso date to the nearest minute.
     *
     * @author Per Søderlind
     * @param {Date} date
     * @returns {string} ISO date
     */
    roundedISODate(date) {
      date.setMinutes(date.getMinutes() - date.getTimezoneOffset());
      const roundedNow = this.roundMinutes(date);
      const roundedNowISO = roundedNow.toISOString().slice(0, 16);

      return roundedNowISO;
    }

    /**
     * Round the date to the nearest hour
     * @param {Date} date
     * @return {Date}
     * @since 2.0.0
     */
    roundMinutes(date) {
      date.setHours(date.getHours() + Math.round(date.getMinutes() / 60));
      date.setMinutes(0, 0, 0);
      return date;
    }
    /**
     * Mark mandatory field with color red.
     *
     * @param {*} settingModel
     * @param {*} dataModel
     * @param {*} view
     */
    changeColor(settingModel, dataModel, view) {
      const label = view.el.querySelector("label");
      Object.assign(label.style, {
        color: "red",
      });
      const dashicon = view.el.getElementsByClassName(
        "dashicons-admin-comments"
      )[0];
      Object.assign(dashicon.style, {
        color: "red",
      });
    }
  };

  new iCalendarsSettings();
});
