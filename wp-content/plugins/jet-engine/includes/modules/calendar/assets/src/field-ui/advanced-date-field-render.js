class JetEngineRenderAdvancedDateField {

	constructor( selector, args ) {

		const {
			labels,
			templateEngine,
			initCallback
		} = args;

		this.initCallbacks = [];
		this.templateEngine = templateEngine || 'wp';

		if (  initCallback ) {
			this.addInitCallback( initCallback );
		}

		this.labels = {
			...{
				startDate: 'Start date',
				hasEndDate: 'Has end date',
				endTime: 'End time',
				isRecurring: 'Is recurring?',
				repeat: 'Repeat',
				every: 'Every',
				onDay: 'on day',
				onThe: 'on the',
				recurring: 'Recurring',
				recurringPeriod: 'Recurring period',
				weekDays: 'Week days',
				monthlyType: 'Monthly type',
				monthDay: 'Month day',
				monthDayType: 'Month day type',
				monthDayTypeValue: 'Month day type value',
				month: 'Month',
				end: 'End',
				after: 'After',
				onDate: 'On Date',
				iterations: 'iterations',
				confirmDelete: 'Are you sure?',
				confirmDeleteYes: 'Yes',
				confirmDeleteNo: 'No',
				addDate: '+ Add date',
				recurringLabelDaily: 'Daily',
				recurringLabelWeekly: 'Weekly',
				recurringLabelMonthly: 'Monthly',
				recurringLabelYearly: 'Yearly',
				recurringSubLabelDaily: 'day(s)',
				recurringSubLabelWeekly: 'week(s)',
				recurringSubLabelMonthly: 'month(s)',
				recurringSubLabelYearly: 'year(s)',
				first: 'First',
				second: 'Second',
				third: 'Third',
				fourth: 'Fourth',
				last: 'Last',
				day: 'Day',
				weekDayMon: 'Mon',
				weekDayTue: 'Tue',
				weekDayWed: 'Wed',
				weekDayThu: 'Thu',
				weekDayFri: 'Fri',
				weekDaySat: 'Sat',
				weekDaySun: 'Sun',
				monthJan: 'Jan',
				monthFeb: 'Feb',
				monthMar: 'Mar',
				monthApr: 'Apr',
				monthMay: 'May',
				monthJun: 'Jun',
				monthJul: 'Jul',
				monthAug: 'Aug',
				monthSep: 'Sep',
				monthOct: 'Oct',
				monthNov: 'Nov',
				monthDec: 'Dec',
			},
			...labels
		};

		this.setup( selector );
		this.render();
		this.events();

	}

	addInitCallback( callback ) {
		this.initCallbacks.push( callback );
	}

	setup( selector ) {

		this.$container = jQuery( '<div></div>' ).appendTo( selector );
		this.$input = selector.find( 'input[type="hidden"]' );
		this.fieldName = this.$input.attr( 'name' );
		this.required = this.$input.attr( 'required' ) || false;
		this.value = this.$input.val() || '{}';
		this.allowTime = this.$input.data( 'allow-time' );
		this.dataFormat = this.$input.data( 'format' ) || 'rrule';

		try {
			this.value = JSON.parse( this.value );
		} catch( e ) {
			this.value = {};
		}

		this.dates = this.value.dates || [];
		this.date = this.value.date || '';
		this.time = this.value.time || '';
		this.isEndDate = this.value.is_end_date || false;
		this.endDate = this.value.end_date || '';
		this.endTime = this.value.end_time || '';
		this.isRecurring = this.value.is_recurring || false;
		this.recurring = this.value.recurring || 'weekly';
		this.recurringPeriod = this.value.recurring_period || 1;
		this.weekDays = this.value.week_days || [];
		this.monthlyType = this.value.monthly_type || 'on_day';
		this.monthDay = this.value.month_day || 1;
		this.monthDayType = this.value.month_day_type || 'first';
		this.monthDayTypeValue = this.value.month_day_type_value || 'Sun';
		this.month = this.value.month || 'Jan';
		this.end = this.value.end || 'after';
		this.endAfter = this.value.end_after || 5;
		this.endAfterDate = this.value.end_after_date || '';

		if ( ! this.dates.length ) {

			this.dates = [];

			this.dates.push( {
				date: this.date,
				time: this.time,
				isEndDate: this.isEndDate,
				endDate: this.endDate,
				endTime: this.endTime
			} );
		}

		// fix saved switchers
		const switchers = [
			'isEndDate',
			'isRecurring'
		];

		for ( var i = 0; i < switchers.length; i++ ) {
			if ( 1 == this[ switchers[ i ] ] ) {
				this[ switchers[ i ] ] = true;
			} else {
				this[ switchers[ i ] ] = false;
			}
		}

	}

	getProps() {
		return {
			dates: this.dates,
			date: this.date,
			time: this.time,
			is_end_date: this.isEndDate,
			end_date: this.endDate,
			end_time: this.endTime,
			is_recurring: this.isRecurring,
			recurring: this.recurring,
			recurring_period: this.recurringPeriod,
			week_days: this.weekDays,
			monthly_type: this.monthlyType,
			month_day: this.monthDay,
			month_day_type: this.monthDayType,
			month_day_type_value: this.monthDayTypeValue,
			month: this.month,
			end: this.end,
			end_after: this.endAfter,
			end_after_date: this.endAfterDate,
			data_format: this.dataFormat,
		}
	}

	render() {

		if ( 'rrule' === this.dataFormat ) {
			this.renderRecurring();
		} else {
			this.renderCustom();
		}

	}

	renderCustom() {

		const repeaterWrapper = document.createElement( 'div' );
		const repeaterNew = document.createElement( 'button' );

		repeaterWrapper.classList.add( 'jet-engine-advanced-date-field__repeater' );

		repeaterNew.classList.add( 'jet-engine-advanced-date-field__repeater-new' );
		repeaterNew.setAttribute( 'type', 'button' );
		repeaterNew.innerHTML = this.labels.addDate;

		for ( var i = 0; i < this.dates.length; i++ ) {

			if ( ! this.dates[ i ].uid ) {
				this.dates[ i ].uid = this.randomID();
			}

			repeaterWrapper.append( this.getNewRepeaterEl( this.formatData( this.dates[ i ] ) ) );
		}

		repeaterNew.addEventListener( 'click', ( event ) => {

			event.preventDefault();

			let newDate = {
				uid: this.randomID(),
				date: '',
				time: '',
				isEndDate: false,
				endDate: '',
				endTime: '',
			};

			this.dates.push( newDate );
			repeaterWrapper.append( this.getNewRepeaterEl( newDate ) );

		});

		this.$container.append( repeaterWrapper );
		this.$container.append( repeaterNew );

	}

	formatData( data ) {
		const formatted = {};

		for ( const key in data ) {
			let newKey = key.replace( /_([a-z])/g, ( g ) => { return g[1].toUpperCase(); } )
			formatted[ newKey ] = data[ key ];
		}

		return formatted;

	}

	randomID() {
		const min = 1000;
		const max = 9999;

		return Math.floor( Math.random() * ( max - min + 1 ) + min );
	}

	getNewRepeaterEl( data ) {

		if ( ! this.fieldTemplate ) {
			if ( 'wp' === this.templateEngine ) {
				this.fieldTemplate = wp.template( 'jet-engine-advanced-date-field-custom' );
			} else {
				this.fieldTemplate = this.templateEngine( 'base-date' );
			}
		}

		const defaults = {
			required: false,
			allowTime: this.allowTime,
			fieldName: this.fieldName + '[dates][' + data.uid + ']',
			labels: this.labels,
		};

		data = { ...defaults, ...data };

		const newRow = document.createElement( 'div' );

		newRow.classList.add( 'jet-engine-advanced-date-field__repeater-row' );
		newRow.innerHTML = this.fieldTemplate( data );
		newRow.dataset.index = data.uid;
		newRow.setAttribute( 'data-index', data.uid );

		const deleteButtonWrap = document.createElement( 'div' );
		const deleteButton = document.createElement( 'button' );
		const deleteButtonConfirm = document.createElement( 'div' );

		deleteButtonWrap.classList.add( 'jet-engine-advanced-date-field__repeater-delete' );

		deleteButton.classList.add( 'jet-engine-advanced-date-field__repeater-delete-button' );
		deleteButton.setAttribute( 'type', 'button' );

		deleteButton.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"><rect x="0" fill="none" width="20" height="20"/><g><path d="M12 4h3c.6 0 1 .4 1 1v1H3V5c0-.6.5-1 1-1h3c.2-1.1 1.3-2 2.5-2s2.3.9 2.5 2zM8 4h3c-.2-.6-.9-1-1.5-1S8.2 3.4 8 4zM4 7h11l-.9 10.1c0 .5-.5.9-1 .9H5.9c-.5 0-.9-.4-1-.9L4 7z"/></g></svg>';

		deleteButtonConfirm.classList.add( 'jet-engine-advanced-date-field__repeater-confirm' );
		deleteButtonConfirm.innerHTML = `${this.labels.confirmDelete} <span class="jet-engine-advanced-date-field__repeater-confirm-yes">${this.labels.confirmDeleteYes}</span><span class="jet-engine-advanced-date-field__repeater-confirm-no">${this.labels.confirmDeleteNo}</span>`;

		deleteButtonWrap.append( deleteButton );
		deleteButtonWrap.append( deleteButtonConfirm );

		deleteButton.addEventListener( 'click', ( event ) => {
			event.preventDefault();
			deleteButtonWrap.classList.add( 'show-confirm' );
		} );

		deleteButtonConfirm.addEventListener( 'click', ( event ) => {

			event.preventDefault();
			event.stopPropagation();

			if ( event.target.classList.contains( 'jet-engine-advanced-date-field__repeater-confirm-yes' ) ) {
				newRow.remove();
				this.dates = this.dates.filter( ( item ) => { return item.uid != data.uid } );
				this.$input.attr( 'value', JSON.stringify( this.getProps() ) );
			}

			if ( event.target.classList.contains( 'jet-engine-advanced-date-field__repeater-confirm-no' ) ) {
				deleteButtonWrap.classList.remove( 'show-confirm' );
			}

		} );

		newRow.append( deleteButtonWrap );

		this.initCallbacks.forEach( ( callback ) => {
			callback( {
				instance: this,
				newRow: newRow,
			} );
		} );

		return newRow;
	}

	renderRecurring() {

		let fieldTemplate;

		if ( 'wp' === this.templateEngine ) {
			fieldTemplate = wp.template( 'jet-engine-advanced-date-field-rrule' );
		} else {
			fieldTemplate = this.templateEngine();
		}

		const templateData = {
			labels: this.labels,
			weekdaysConfig: [ {
				value: 1,
				label: this.labels.weekDayMon,
			}, {
				value: 2,
				label: this.labels.weekDayTue,
			}, {
				value: 3,
				label: this.labels.weekDayWed,
			}, {
				value: 4,
				label: this.labels.weekDayThu,
			}, {
				value: 5,
				label: this.labels.weekDayFri,
			}, {
				value: 6,
				label: this.labels.weekDaySat,
			},{
				value: 7,
				label: this.labels.weekDaySun,
			} ],
			months: [ {
				value: 1,
				label: this.labels.monthJan,
			}, {
				value: 2,
				label: this.labels.monthFeb,
			}, {
				value: 3,
				label: this.labels.monthMar,
			}, {
				value: 4,
				label: this.labels.monthApr,
			}, {
				value: 5,
				label: this.labels.monthMay,
			}, {
				value: 6,
				label: this.labels.monthJun,
			}, {
				value: 7,
				label: this.labels.monthJul,
			}, {
				value: 8,
				label: this.labels.monthAug,
			}, {
				value: 9,
				label: this.labels.monthSep,
			}, {
				value: 10,
				label: this.labels.monthOct,
			}, {
				value: 11,
				label: this.labels.monthNov,
			}, {
				value: 12,
				label: this.labels.monthDec,
			} ],
			recurrings: [
				{
					value: 'daily',
					label: this.labels.recurringLabelDaily,
					sublabel: this.labels.recurringSubLabelDaily
				},
				{
					value: 'weekly',
					label: this.labels.recurringLabelWeekly,
					sublabel: this.labels.recurringSubLabelWeekly
				},
				{
					value: 'monthly',
					label: this.labels.recurringLabelMonthly,
					sublabel: this.labels.recurringSubLabelMonthly
				},
				{
					value: 'yearly',
					label: this.labels.recurringLabelYearly,
					sublabel: this.labels.recurringSubLabelYearly
				}
			],
			required: this.required,
			fieldName: this.fieldName,
			date: this.date,
			time: this.time,
			isEndDate: this.isEndDate,
			endDate: this.endDate,
			endTime: this.endTime,
			isRecurring: this.isRecurring,
			recurring: this.recurring,
			recurringPeriod: this.recurringPeriod,
			weekDays: this.weekDays,
			monthlyType: this.monthlyType,
			monthDay: this.monthDay,
			monthDayType: this.monthDayType,
			monthDayTypeValue: this.monthDayTypeValue,
			month: this.month,
			end: this.end,
			endAfter: this.endAfter,
			endAfterDate: this.endAfterDate,
			allowTime: this.allowTime,
		};

		this.$container.html( fieldTemplate( templateData ) );

		this.initCallbacks.forEach( ( callback ) => {
			callback( {
				instance: this,
			} );
		} );
	}

	selectors( which ) {

		const selectors = {
			date: 'input[name="' + this.fieldName + '[date]"]',
			time: 'input[name="' + this.fieldName + '[time]"]',
			isEndDate: 'input[name="' + this.fieldName + '[is_end_date]"]',
			endDate: 'input[name="' + this.fieldName + '[end_date]"]',
			endTime: 'select[name="' + this.fieldName + '[end_time]"]',
			isRecurring: 'input[name="' + this.fieldName + '[is_recurring]"]',
			recurring: 'select[name="' + this.fieldName + '[recurring]"]',
			recurringPeriod: 'input[name="' + this.fieldName + '[recurring_period]"]',
			weekDays: 'input[name="' + this.fieldName + '[week_days][]"]',
			monthlyType: 'input[name="' + this.fieldName + '[monthly_type]"]',
			monthDay: 'select[name="' + this.fieldName + '[month_day]"]',
			monthDayType: 'select[name="' + this.fieldName + '[month_day_type]"]',
			monthDayTypeValue: 'select[name="' + this.fieldName + '[month_day_type_value]"]',
			month: 'select[name="' + this.fieldName + '[month]"]',
			end: 'select[name="' + this.fieldName + '[end]"]',
			endAfter: 'input[name="' + this.fieldName + '[end_after]"]',
			endAfterDate: 'input[name="' + this.fieldName + '[end_after_date]"]',
		}

		if ( which ) {
			return selectors[ which ];
		} else {
			return selectors;
		}

	}

	update( data, silent ) {

		silent = silent || false;

		let updated = false;

		for ( const key in data ) {
			if ( this[ key ] !== data[ key ] ) {
				this[ key ] = data[ key ];
				updated = true;
			}
		}

		this.$input.attr( 'value', JSON.stringify( this.getProps() ) );

		if ( ! silent && updated ) {
			this.render();
		}

	}

	events() {
		if ( 'rrule' === this.dataFormat ) {
			this.eventsRRule();
		} else {
			this.eventsCustom();
		}
	}

	eventsCustom() {

		this.$container.on( 'change', '.jet-engine-advanced-date-field--switcher, .field-type-switcher input', ( event ) => {

			const $row  = jQuery( event.target ).closest( '.jet-engine-advanced-date-field__repeater-row' );
			const index = $row.data( 'index' );

			if ( 'isEndDate' === event.target.dataset.key ) {
				for ( var i = 0; i < this.dates.length; i++) {
					if ( this.dates[ i ].uid == index ) {
						if ( undefined === this.dates[ i ].isEndDate ) {
							this.dates[ i ].isEndDate = event.target.checked;
						} else {
							this.dates[ i ].isEndDate = ! this.dates[ i ].isEndDate;
						}
						$row.replaceWith( this.getNewRepeaterEl( this.dates[ i ] ) );
						break;
					}
				}
			}

			this.$input.attr( 'value', JSON.stringify( this.getProps() ) );

		} );

		jQuery( window ).on( 'cx-control-change change', ( event ) => {

			const target = event.input || jQuery( event.target );

			if ( target && target.hasClass( 'jet-engine-advanced-date-field--control' ) ) {

				const $row  = target.closest( '.jet-engine-advanced-date-field__repeater-row' );
				const index = $row.data( 'index' );

				for ( var i = 0; i < this.dates.length; i++) {
					if ( this.dates[ i ].uid == index ) {
						let value = event.controlStatus || event.target.value;
						this.dates[ i ][ target.data( 'key' ) ] = value;
						break;
					}
				}

				this.$input.attr( 'value', JSON.stringify( this.getProps() ) );
			}
		} );

	}

	eventsRRule() {

		const switchers = [
			'isEndDate',
			'isRecurring'
		];

		const regularEnvents = [
			'end',
			'recurring',
			'month'
		];

		const silentEvents = [
			'recurringPeriod',
			'monthlyType',
			'monthDay',
			'monthDayType',
			'monthDayTypeValue',
			'endAfter',
		];

		const dates = {
			date: this.fieldName + '[date]',
			time: this.fieldName + '[time]',
			endDate: this.fieldName + '[end_date]',
			endTime: this.fieldName + '[end_time]',
			endAfterDate: this.fieldName + '[end_after_date]'
		};

		for ( var i = 0; i < switchers.length; i++ ) {
			this.$container.on( 'change', this.selectors( switchers[ i ] ), ( ( key, event ) => {
				this.update( { [ key ]: event.target.checked } );
			} ).bind( undefined, switchers[ i ] ) );
		}

		for ( var i = 0; i < regularEnvents.length; i++ ) {
			this.$container.on( 'change', this.selectors( regularEnvents[ i ] ), ( ( key, event ) => {
				this.update( { [ key ]: event.target.value } );
			} ).bind( undefined, regularEnvents[ i ] ) );
		}

		for ( var i = 0; i < silentEvents.length; i++ ) {
			this.$container.on( 'change', this.selectors( silentEvents[ i ] ), ( ( key, event ) => {
				this.update( { [ key ]: event.target.value }, true );
			} ).bind( undefined, silentEvents[ i ] ) );
		}

		for ( const prop in dates ) {

			// Fallback for Admin UI
			jQuery( window ).on( 'cx-control-change', ( ( key, name, event ) => {
				if ( event.controlName == name ) {
					this.update( { [ key ]: event.controlStatus }, true );
				}
			} ).bind( undefined, prop, dates[ prop ] ) );

			this.$container.on( 'change', this.selectors( dates[ prop ] ), ( ( key, name, event ) => {
				if ( event?.target?.name == name ) {
					this.update( { [ key ]: event.target.value }, true );
				}
			} ).bind( undefined, prop, dates[ prop ] ) );
		}

		this.$container.on( 'change', this.selectors( 'weekDays' ), ( event ) => {

			const newDays = [];
			const checked = document.querySelectorAll( this.selectors( 'weekDays' ) + ':checked' );

			if ( checked && checked.length ) {
				for ( var i = 0; i < checked.length; i++ ) {
					newDays.push( checked[ i ].value );
				}
			}

			this.update( { weekDays: newDays } );

		} );

	}

}

export default JetEngineRenderAdvancedDateField;