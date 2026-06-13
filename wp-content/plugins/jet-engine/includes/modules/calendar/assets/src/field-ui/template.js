/**
 * Advanced Date field template class.
 * Works in similar way as back bone template.
 * Class contains HTML markup with template literals.
 * From constructor receives dynamic data and renders tamplate accoding retrieved data.
 * @class
 */

class JetEngineAdvancedDateFieldTemplate {

	render( data ) {
		return this.getTemplate( data );
	}

	/**
	 * Get template
	 *
	 * @return {string}
	 */
	getTemplate( data ) {
		return `
			${ this.getBaseDateFieldTemplate( data ) }
			${ this.getRecurringFieldTemplate( data ) }
		`;
	}

	getBaseDateFieldTemplate( data ) {
		return `
			${ this.getStartDateFieldTemplate( data ) }
			${ this.getEndDateFieldTemplate( data ) }
		`;
	}

	getStartDateFieldTemplate( data ) {
		return `
			<div class="jet-engine-advanced-date-field__date">
				<span class="jet-engine-advanced-date-field__label">${ data.labels.startDate }</span>
				<div class="jet-engine-advanced-date-field__date-warp ${ data.required ? 'is-required' : '' }" data-control-name="${ data.fieldName }[date]">
					<div class="jet-engine-advanced-date-field__date-controls">
						<input
							type="date"
							class="jet-engine-advanced-date-field__date-input jet-engine-advanced-date-field--control"
							name="${ data.fieldName }[date]"
							placeholder="Select date..."
							value="${ data.date }"
							data-key="date"
							${ data.required ? 'required' : '' }
						>
						${ data.allowTime ? `
							<input
								type="time"
								class="jet-engine-advanced-date-field__time-input jet-engine-advanced-date-field--control"
								name="${ data.fieldName }[time]"
								placeholder="Set time..."
								value="${ data.time }"
								data-key="time"
								${ data.required ? 'required' : '' }
							>
						` : '' }
					</div>
					<div class="cx-control__error"></div>
				</div>
			</div>
		`;
	}

	getEndDateFieldTemplate( data ) {
		return `
			<div class="jet-engine-advanced-date-field__date">
				<span class="jet-engine-advanced-date-field__label">${ data.labels.hasEndDate }</span>
				<div class="jet-engine-advanced-date-field__end-date-controls">
					<div class="field-type-switcher">
						<input id="${ data.fieldName }__is_end_date" name="${ data.fieldName }[is_end_date]" type="checkbox" role="switch" class="jet-form-builder__field" value="1" ${ data.isEndDate ? 'checked' : '' } data-calculate="1" data-jfb-sync="null" data-key="isEndDate">
					</div>
					${ data.isEndDate ? `
						<div class="jet-engine-advanced-date-field__date-warp ${ data.required ? 'cx-control-required' : '' }" data-control-name="${ data.fieldName }[end_date]">
							<div class="jet-engine-advanced-date-field__date-controls">
								<input
									type="date"
									class="jet-engine-advanced-date-field__end-date-input jet-engine-advanced-date-field--control"
									name="${ data.fieldName }[end_date]"
									placeholder="Select date..."
									value="${ data.endDate }"
									data-key="endDate"
									${ data.required ? 'required' : '' }
								>
								${ data.allowTime ? `
									<input
										type="time"
										class="jet-engine-advanced-date-field__end-time-input jet-engine-advanced-date-field--control"
										name="${ data.fieldName }[end_time]"
										placeholder="Set time..."
										value="${ data.endTime }"
										data-key="endTime"
										${ data.required ? 'required' : '' }
									>
								` : '' }
							</div>
							<div class="cx-control__error"></div>
						</div>
					` : '' }
				</div>
			</div>
		`;
	}

	getRecurringFieldTemplate( data ) {
		return `
			<div class="jet-engine-advanced-date-field__is-recurring">
				<label class="jet-engine-advanced-date-field__label" for="${ data.fieldName }__is_recurring">
					${ data.labels.isRecurring }
				</label>
				<div class="field-type-switcher">
					<input id="${ data.fieldName }__is_recurring" name="${ data.fieldName }[is_recurring]" type="checkbox" role="switch" class="jet-form-builder__field" value="1" ${ data.isRecurring ? 'checked' : '' } data-calculate="1" data-jfb-sync="null" data-key="isRecurring">
				</div>
			</div>
			${ data.isRecurring ? `
				<div class="jet-engine-advanced-date-field__recurring-wrap">
					<div class="jet-engine-advanced-date-field__recurring-row">
						<div class="jet-engine-advanced-date-field__recurring-label jet-engine-advanced-date-field__label">
							${ data.labels.repeat }
						</div>
						<div class="jet-engine-advanced-date-field__recurring-content">
							<select name="${ data.fieldName }[recurring]" class="cx-ui-select">
								${ data.recurrings.map(recurring => `
									<option
										value="${ recurring.value }"
										${ data.recurring === recurring.value ? 'selected' : '' }
									>
										${ recurring.label }
									</option>
								`).join('') }
							</select>
							<div class="jet-engine-advanced-date-field__recurring-label">
								${ data.labels.every }
							</div>
							<input
								type="number"
								name="${ data.fieldName }[recurring_period]"
								min="1"
								value="${ data.recurringPeriod }"
								class="cx-ui-text"
							>
							${ data.recurrings.map(recurring =>
								data.recurring === recurring.value ? `
									<div class="jet-engine-advanced-date-field__recurring-label">
										${ recurring.sublabel }
									</div>
								` : ''
							).join('') }
						</div>
					</div>
					${ data.recurring !== 'daily' ? `
						<div class="jet-engine-advanced-date-field__recurring-row">
							<div class="jet-engine-advanced-date-field__recurring-label jet-engine-advanced-date-field__label label-weekdays">
								&nbsp;
							</div>
							<div class="jet-engine-advanced-date-field__recurring-content">
								${ data.recurring === 'weekly' ? `
									<div class="jet-engine-advanced-date-field__weekdays">
										${ data.weekdaysConfig.map(day => `
											<label aria-label="${ day.label }">
												<input
													type="checkbox"
													value="${ day.value }"
													name="${ data.fieldName }[week_days][]"
													${ data.weekDays.includes('' + day.value) ? 'checked' : '' }
												>
												<span class="jet-engine-advanced-date-field__weekday-label">
													${ day.label }
												</span>
												<span class="jet-engine-advanced-date-field__weekday-marker"></span>
											</label>
										`).join('') }
									</div>
								` : '' }
								${ (data.recurring === 'monthly' || data.recurring === 'yearly') ? `
									<div class="jet-engine-advanced-date-field__monthly">
										<div class="jet-engine-advanced-date-field__monthly-row">
											<label>
												<input
													type="radio"
													value="on_day"
													name="${ data.fieldName }[monthly_type]"
													${ data.monthlyType === 'on_day' ? 'checked' : '' }
												>
												${ data.labels.onDay }
											</label>
											${ data.recurring === 'yearly' ? `
												<select name="${ data.fieldName }[month]" class="cx-ui-select">
													${ data.months.map(month => `
														<option
															value="${ month.value }"
															${ month.value == data.month ? 'selected' : '' }
														>
															${ month.label }
														</option>
													`).join('') }
												</select>
											` : '' }
											<select name="${ data.fieldName }[month_day]" class="cx-ui-select">
												${ Array.from({ length: 31 }, (_, i) => i + 1).map(i => `
													<option
														value="${ i }"
														${ i == data.monthDay ? 'selected' : '' }
													>
														${ i }
													</option>
												`).join('') }
											</select>
										</div>
										<div class="jet-engine-advanced-date-field__monthly-row">
											<label>
												<input
													type="radio"
													value="on_day_type"
													name="${ data.fieldName }[monthly_type]"
													${ data.monthlyType === 'on_day_type' ? 'checked' : '' }
												>
												${ data.labels.onThe }
											</label>
											<select name="${ data.fieldName }[month_day_type]" class="cx-ui-select">
												<option value="first" ${ data.monthDayType === 'first' ? 'selected' : '' }>${ data.labels.first }</option>
												<option value="second" ${ data.monthDayType === 'second' ? 'selected' : '' }>${ data.labels.second }</option>
												<option value="third" ${ data.monthDayType === 'third' ? 'selected' : '' }>${ data.labels.third }</option>
												<option value="fourth" ${ data.monthDayType === 'fourth' ? 'selected' : '' }>${ data.labels.fourth }</option>
												<option value="last" ${ data.monthDayType === 'last' ? 'selected' : '' }>${ data.labels.last }</option>
											</select>
											<select name="${ data.fieldName }[month_day_type_value]" class="cx-ui-select">
												${ data.weekdaysConfig.map(day => `
													<option
														value="${ day.value }"
														${ day.value == data.monthDayTypeValue ? 'selected' : '' }
													>
														${ day.label }
													</option>
												`).join('') }
												<option value="day" ${ data.monthDayTypeValue === 'day' ? 'selected' : '' }>${ data.labels.day }</option>
											</select>
											${ data.recurring === 'yearly' ? `
												<select name="${ data.fieldName }[month]" class="cx-ui-select">
													${ data.months.map(month => `
														<option
															value="${ month.value }"
															${ month.value == data.month ? 'selected' : '' }
														>
															${ month.label }
														</option>
													`).join('') }
												</select>
											` : '' }
										</div>
									</div>
								` : '' }
							</div>
						</div>
					` : '' }
					<div class="jet-engine-advanced-date-field__recurring-row">
						<div class="jet-engine-advanced-date-field__recurring-label jet-engine-advanced-date-field__label">
							${ data.labels.end }
						</div>
						<div class="jet-engine-advanced-date-field__recurring-content">
							<select name="${ data.fieldName }[end]" class="cx-ui-select">
								<option value="after" ${ data.end === 'after' ? 'selected' : '' }>${ data.labels.after }</option>
								<option value="on_date" ${ data.end === 'on_date' ? 'selected' : '' }>${ data.labels.onDate }</option>
							</select>
							${ data.end === 'after' ? `
								<input
									type="number"
									name="${ data.fieldName }[end_after]"
									min="2"
									value="${ data.endAfter }"
									class="cx-ui-text"
								>
								<div class="jet-engine-advanced-date-field__recurring-label">
									${ data.labels.iterations }
								</div>
							` : '' }
							${ data.end === 'on_date' ? `
								<input
									type="date"
									class="jet-engine-advanced-date-field__date-input"
									name="${ data.fieldName }[end_after_date]"
									placeholder="Select date..."
									value="${ data.endAfterDate }"
									required
								>
							` : '' }
						</div>
					</div>
				</div>
			` : '' }
		`;
	}

}

export default JetEngineAdvancedDateFieldTemplate;