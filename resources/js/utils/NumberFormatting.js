const ENGLISH_COMPACT_SHORT = new Intl.NumberFormat('en-US', {
  notation: "compact",
  compactDisplay: "short"
});

/**
 * Formats a number based on English locale. On 5 digits, 1200 becomes «12,000», 350000 becomes «350K»
 * @param {number} value
 * @param {Object} [config]
 * @param {number} config.digits - Significant digits (inclusive) to keep before compacting the number 
 * @returns {string} Formatted value
 */
export function CompactNumberFormat(value, config = {}) {
  const { digits = 5 } = config;

  if (value >= Math.pow(10, digits)) {
    return ENGLISH_COMPACT_SHORT.format(value);
  }
  else {
    return Number(value).toLocaleString('en-US');
  }
}
