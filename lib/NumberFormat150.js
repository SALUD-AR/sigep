<!--
/*
 * NumberFormat 1.5.0
 * v1.5.0 - 20-December-2002
 * v1.0.3 - 23-March-2002
 * v1.0.2 - 13-March-2002
 * v1.0.1 - 20-July-2001
 * v1.0.0 - 13-April-2000
 * http://www.mredkj.com
 *
 */

/*
 * NumberFormat -The constructor
 * num - The number to be formatted.
 *  Also refer to setNumber
 */
function NumberFormat(num)
{
	// constants
	this.COMMA = ',';
	this.PERIOD = '.';
	this.DASH = '-'; // v1.5.0 - new - used internally
	this.LEFT_PAREN = '('; // v1.5.0 - new - used internally
	this.RIGHT_PAREN = ')'; // v1.5.0 - new - used internally
	this.LEFT_OUTSIDE = 0; // v1.5.0 - new - currency
	this.LEFT_INSIDE = 1;  // v1.5.0 - new - currency
	this.RIGHT_INSIDE = 2;  // v1.5.0 - new - currency
	this.RIGHT_OUTSIDE = 3;  // v1.5.0 - new - currency
	this.LEFT_DASH = 0; // v1.5.0 - new - negative
	this.RIGHT_DASH = 1; // v1.5.0 - new - negative
	this.PARENTHESIS = 2; // v1.5.0 - new - negative

	// member variables
	this.num;
	this.numOriginal;
	this.hasSeparators = false;  // v1.5.0 - new
	this.separatorValue;  // v1.5.0 - new
	this.inputDecimalValue; // v1.5.0 - new
	this.decimalValue;  // v1.5.0 - new
	this.negativeFormat; // v1.5.0 - new
	this.negativeRed; // v1.5.0 - new
	this.hasCurrency;  // v1.5.0 - modified
	this.currencyPosition;  // v1.5.0 - new
	this.currencyValue;  // v1.5.0 - modified
	this.places;

	// external methods
	this.setNumber = setNumberNF;
	this.toUnformatted = toUnformattedNF;
	this.setInputDecimal = setInputDecimalNF; // v1.5.0 - new
	this.setSeparators = setSeparatorsNF; // v1.5.0 - new - for separators and decimals
	this.setCommas = setCommasNF;
	this.setNegativeFormat = setNegativeFormatNF; // v1.5.0 - new
	this.setNegativeRed = setNegativeRedNF; // v1.5.0 - new
	this.setCurrency = setCurrencyNF;
	this.setCurrencyPrefix = setCurrencyPrefixNF;
	this.setCurrencyValue = setCurrencyValueNF; // v1.5.0 - new - setCurrencyPrefix uses this
	this.setCurrencyPosition = setCurrencyPositionNF; // v1.5.0 - new - setCurrencyPrefix uses this
	this.setPlaces = setPlacesNF;
	this.toFormatted = toFormattedNF;
	this.toPercentage = toPercentageNF;
	this.getOriginal = getOriginalNF;

	// internal methods
	this.getRounded = getRoundedNF;
	this.preserveZeros = preserveZerosNF;
	this.justNumber = justNumberNF;

	// setup defaults
	this.setInputDecimal(this.PERIOD); // v1.5.0 - new
	this.setNumber(num); // make sure setInputDecimal is called before setNumber
	this.setCommas(true);
	this.setNegativeFormat(this.LEFT_DASH); // v1.5.0 - new
	this.setNegativeRed(false); // v1.5.0 - new
	this.setCurrency(true);
	this.setCurrencyPrefix('$');
	this.setPlaces(2);
}

/*
 * setInputDecimal
 * val - The decimal value for the input.
 *
 * v1.5.0 - new
 */
function setInputDecimalNF(val)
{
	this.inputDecimalValue = val;
}

/*
 * setNumber - Sets the number
 * num - The number to be formatted
 * 
 * If there is a non-period decimal format for the input,
 * setInputDecimal should be called before calling setNumber.
 *
 * v1.5.0 - modified
 */
function setNumberNF(num)
{
	this.numOriginal = num;
	this.num = this.justNumber(num);
}

/*
 * toUnformatted - Returns the number as just a number.
 * If the original value was '100,000', then this method will return the number 100000
 * v1.0.2 - Modified comments, because this method no longer returns the original value.
 */
function toUnformattedNF()
{
	return (this.num);
}

/*
 * getOriginal - Returns the number as it was passed in, which may include non-number characters.
 * This function is new in v1.0.2
 */
function getOriginalNF()
{
	return (this.numOriginal);
}

/*
 * setNegativeFormat - How to format a negative number.
 * 
 * format - The format. Use one of the following constants.
 * LEFT_DASH   example: -1000
 * RIGHT_DASH  example: 1000-
 * PARENTHESIS example: (1000)
 *
 * v1.5.0 - new
 */
function setNegativeFormatNF(format)
{
	this.negativeFormat = format;
}

/*
 * setNegativeRed - Format the number red if it's negative.
 * 
 * isRed - true, to format the number red if negative, black if positive;
 *  false, for it to always be black font.
 *
 * v1.5.0 - new
 */
function setNegativeRedNF(isRed)
{
	this.negativeRed = isRed;
}

/*
 * setSeparators - One purpose of this method is to set a
 *  switch that indicates if there should be separators between groups of numbers.
 *  Also, can use it to set the values for the separator and decimal.
 *  For example, in the value 1,000.00
 *   The comma (,) is the separator and the period (.) is the decimal.
 *
 * Both separator or decimal are not required.
 * The separator and decimal cannot be the same value. If they are, decimal with be changed.
 * Can use the following constants (via the instantiated object) for separator or decimal:
 *  COMMA
 *  PERIOD
 * 
 * isC - true, if there should be separators; false, if there should be no separators
 * separator - the value of the separator.
 * decimal - the value of the decimal.
 *
 * v1.5.0 - new
 */
function setSeparatorsNF(isC, separator, decimal)
{
	this.hasSeparators = isC;
	
	// Make sure a separator was passed in
	if (separator == null) separator = this.COMMA;
	
	// Make sure a decimal was passed in
	if (decimal == null) decimal = this.PERIOD;
	
	// Additionally, make sure the values aren't the same.
	//  When the separator and decimal both are periods, make the decimal a comma.
	//  When the separator and decimal both are any other value, make the decimal a period.
	if (separator == decimal)
	{
		this.decimalValue = (decimal == this.PERIOD) ? this.COMMA : this.PERIOD;
	}
	else
	{
		this.decimalValue = decimal;
	}
	
	// Since the decimal value changes if decimal and separator are the same,
	// the separator value can keep its setting.
	this.separatorValue = separator;

}

/*
 * setCommas - Sets a switch that indicates if there should be commas.
 * The separator value is set to a comma and the decimal value is set to a period.
 * isC - true, if the number should be formatted with separators (commas); false, if no separators
 *
 * v1.5.0 - modified
 */
function setCommasNF(isC)
{
	this.setSeparators(isC, this.COMMA, this.PERIOD);
}

/*
 * setCurrency - Sets a switch that indicates if should be displayed as currency
 * isC - true, if should be currency; false, if not currency
 */
function setCurrencyNF(isC)
{
	this.hasCurrency = isC;
}

/*
 * setCurrencyPrefix - Sets the symbol for currency.
 * val - The symbol
 */
function setCurrencyValueNF(val)
{
	this.currencyValue = val;
}

/*
 * setCurrencyPrefix - Sets the symbol for currency.
 * The symbol will show up on the left of the numbers and outside a negative sign.
 * cp - The symbol
 *
 * v1.5.0 - modified - This now calls setCurrencyValue and setCurrencyPosition(this.LEFT_OUTSIDE)
 */
function setCurrencyPrefixNF(cp)
{
	this.setCurrencyValue(cp);
	this.setCurrencyPosition(this.LEFT_OUTSIDE);
}

/*
 * setCurrencyPosition - Sets the position for currency,
 *  which includes position relative to the numbers and negative sign.
 * cp - The position. Use one of the following constants.
 *  This method does not automatically put the negative sign at the left or right.
 *  They are left by default, and would need to be set right with setNegativeFormat.
 *	LEFT_OUTSIDE  example: $-1.00
 *	LEFT_INSIDE   example: -$1.00
 *	RIGHT_INSIDE  example: 1.00$-
 *	RIGHT_OUTSIDE example: 1.00-$
 *
 * v1.5.0 - new
 */
function setCurrencyPositionNF(cp)
{
	this.currencyPosition = cp
}

/*
 * setPlaces - Sets the precision of decimal places
 * p - The number of places. Any number of places less than or equal to zero is considered zero.
 */
function setPlacesNF(p)
{
	this.places = p;
}

/*
 * toFormatted - Returns the number formatted according to the settings (a string)
 *
 * v1.5.0 - modified
 */
function toFormattedNF()
{
	var pos;
	var nNum = this.num; // v1.0.1 - number as a number
	var nStr;            // v1.0.1 - number as a string
	var splitString = new Array(2);   // v1.5.0

	// round decimal places
	nNum = this.getRounded(nNum);
	nStr = this.preserveZeros(Math.abs(nNum)); // this step makes nNum into a string. v1.0.1 Math.abs

	// the separator and decimal values have to be different
	// this is enforced in justNumber
	if (nStr.indexOf(this.PERIOD) == -1)
	{
		splitString[0] = nStr;
		splitString[1] = '';
	}
	else
	{
		splitString = nStr.split(this.PERIOD, 2);
	}

	// separators
	if (this.hasSeparators)
	{
		pos = splitString[0].length;
		while (pos > 0)
		{
			pos -= 3;
			if (pos <= 0) break;

			splitString[0] = splitString[0].substring(0,pos)
				+ this.separatorValue
				+ splitString[0].substring(pos, splitString[0].length);
		}
	}
	
	// decimal
	if (splitString[1].length > 0)
	{
		nStr = splitString[0] + this.decimalValue + splitString[1];
	}
	else
	{
		nStr = splitString[0];
	}
	
	// negative and currency
	// $[c0] -[n0] $[c1] -[n1] #.#[nStr] -[n2] $[c2] -[n3] $[c3]
	var c0 = '';
	var n0 = '';
	var c1 = '';
	var n1 = '';
	var n2 = '';
	var c2 = '';
	var n3 = '';
	var c3 = '';
	var negSignL = (this.negativeFormat == this.PARENTHESIS) ? this.LEFT_PAREN : this.DASH;
	var negSignR = (this.negativeFormat == this.PARENTHESIS) ? this.RIGHT_PAREN : this.DASH;
		
	if (this.currencyPosition == this.LEFT_OUTSIDE)
	{
		// add currency sign in front, outside of any negative. example: $-1.00	
		if (nNum < 0)
		{
			if (this.negativeFormat == this.LEFT_DASH || this.negativeFormat == this.PARENTHESIS) n1 = negSignL;
			if (this.negativeFormat == this.RIGHT_DASH || this.negativeFormat == this.PARENTHESIS) n2 = negSignR;
		}
		if (this.hasCurrency) c0 = this.currencyValue;
	}
	else if (this.currencyPosition == this.LEFT_INSIDE)
	{
		// add currency sign in front, inside of any negative. example: -$1.00
		if (nNum < 0)
		{
			if (this.negativeFormat == this.LEFT_DASH || this.negativeFormat == this.PARENTHESIS) n0 = negSignL;
			if (this.negativeFormat == this.RIGHT_DASH || this.negativeFormat == this.PARENTHESIS) n3 = negSignR;
		}
		if (this.hasCurrency) c1 = this.currencyValue;
	}
	else if (this.currencyPosition == this.RIGHT_INSIDE)
	{
		// add currency sign at the end, inside of any negative. example: 1.00$-
		if (nNum < 0)
		{
			if (this.negativeFormat == this.LEFT_DASH || this.negativeFormat == this.PARENTHESIS) n0 = negSignL;
			if (this.negativeFormat == this.RIGHT_DASH || this.negativeFormat == this.PARENTHESIS) n3 = negSignR;
		}
		if (this.hasCurrency) c2 = this.currencyValue;
	}
	else if (this.currencyPosition == this.RIGHT_OUTSIDE)
	{
		// add currency sign at the end, outside of any negative. example: 1.00-$
		if (nNum < 0)
		{
			if (this.negativeFormat == this.LEFT_DASH || this.negativeFormat == this.PARENTHESIS) n1 = negSignL;
			if (this.negativeFormat == this.RIGHT_DASH || this.negativeFormat == this.PARENTHESIS) n2 = negSignR;
		}
		if (this.hasCurrency) c3 = this.currencyValue;
	}

	nStr = c0 + n0 + c1 + n1 + nStr + n2 + c2 + n3 + c3;
	
	// negative red
	if (this.negativeRed && nNum < 0)
	{
		nStr = '<font color="red">' + nStr + '</font>';
	}

	return (nStr);
}

/*
 * toPercentage - Format the current number as a percentage.
 * This is separate from most of the regular formatting settings.
 * The exception is the number of decimal places.
 * If a number is 0.123 it will be formatted as 12.3%
 *
 * !! This is an initial version, so it doesn't use many settings.
 * !! should use some of the formatting settings that toFormatted uses.
 * !! probably won't want to use settings like currency.
 *
 * v1.5.0 - new
 */
function toPercentageNF()
{
	nNum = this.num * 100;
	
	// round decimal places
	nNum = this.getRounded(nNum);
	
	return nNum + '%';
}

/*
 * getRounded - Used internally to round a value
 * val - The number to be rounded
 */
function getRoundedNF(val)
{
	var factor;
	var i;

	// round to a certain precision
	factor = 1;
	for (i=0; i<this.places; i++)
	{	factor *= 10; }
	val *= factor;
	val = Math.round(val);
	val /= factor;

	return (val);
}

/*
 * preserveZeros - Used internally to make the number a string
 * 	that preserves zeros at the end of the number
 * val - The number
 */
function preserveZerosNF(val)
{
	var i;

	// make a string - to preserve the zeros at the end
	val = val + '';
	if (this.places <= 0) return val; // leave now. no zeros are necessary - v1.0.1 less than or equal
	
	var decimalPos = val.indexOf('.');
	if (decimalPos == -1)
	{
		val += '.';
		for (i=0; i<this.places; i++)
		{
			val += '0';
		}
	}
	else
	{
		var actualDecimals = (val.length - 1) - decimalPos;
		var difference = this.places - actualDecimals;
		for (i=0; i<difference; i++)
		{
			val += '0';
		}
	}
	
	return val;
}

/*
 * justNumber - Used internally to parse the value into a floating point number.
 * If the value is not set, then return 0.
 * If the value is not a number, then replace all characters that are not 0-9, a decimal point, or a negative sign.
 *
 *  Note: The regular expression cleans up the number, but doesn't get rid of - and .
 *  parseFloat will ignore all values after any character that is NaN.
 *
 *  A number can be entered using special notation.
 *  For example, the following is a valid number: 0.0314E+2
 *
 * This function is new in v1.0.2
 * v1.5.0 - modified
 */
function justNumberNF(val)
{
	val = (val==null) ? 0 : val;
	
	var newVal = val + ""; // to string for first round of parsing

	var isPercentage = false;
	var isFormattedNeg = false;

	// check for percentage
	// v1.5.0
	if (newVal.indexOf('%') != -1)
	{
		newVal = newVal.replace(/\%/g, '');
		
		// mark a flag
		isPercentage = true;
	}
	
	// check for negative
	// This logic does not figure out the real values for double negatives and other oddities.
	// If the value has any dash or both parenthesis, then it is counted as negative.
	// v1.5.0
	if (newVal.indexOf(this.DASH) != -1
	 || (newVal.indexOf(this.LEFT_PAREN) != -1 && newVal.indexOf(this.RIGHT_PAREN) != -1))
	{
		// get rid of the symbols
		// just hardcoding the regular expression to replace all dashes and parenthesis.
		// it's too much unnecessary work to use the constants in this situation.
		newVal = newVal.replace(/[\-\(\)]/g, '');
		
		// mark a flag to add back a negative sign later
		isFormattedNeg = true;
	}
	
	if (this.inputDecimalValue != this.PERIOD)
	{
		// then the separator might be a period
		// get rid of all periods, so they won't be evaluated as a decimal later
		newVal = newVal.replace(/\./g, '');
	}
	
	// replace the first decimal with a period and the rest with blank
	var itrDecimal;
	var tempVal = '';
	var foundDecimal = false;
	for (itrDecimal=0; itrDecimal<newVal.length; itrDecimal++)
	{
		if (newVal.charAt(itrDecimal) == this.inputDecimalValue)
		{
			if (foundDecimal)
			{
				// ignore the rest of the decimals
			}
			else
			{
				tempVal = tempVal + this.PERIOD;
				foundDecimal = true;
			}
		}
		else
		{
			tempVal = tempVal + newVal.charAt(itrDecimal);
		}
	}
  newVal = tempVal;
	
	// now that special input formatting is complete, add negative if applicable
	// v1.5.0
	if (isFormattedNeg) newVal = '-' + newVal;

	// check if a number, otherwise try to figure out what number it is
	if (isNaN(newVal))
	{
		// try taking out non-number characters.
		newVal = parseFloat(newVal.replace(/[^\d\.\-]/g, ''));

		// check if still not a number. Might be undefined, '', etc., so just replace with 0.
		// v1.0.3
		newVal = (isNaN(newVal) ? 0 : newVal); 
	}
	// return 0 in place of infinite numbers.
	// v1.0.3
	else if (!isFinite(newVal))
	{
		newVal = 0;
  }
  
  // now that it's a number, adjust for percentage, if applicable.
  // example. if the number was formatted 24%, then divide by 100 to get 0.24
  // v1.5.0
  if (isPercentage)
  {
  	newVal = newVal / 100;
  }
	
	return newVal;
}

//-->