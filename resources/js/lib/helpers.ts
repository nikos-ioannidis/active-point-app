import moment from 'moment';

/**
 * Format a date string to a localized date string using Moment.js
 * @param dateString - The date string to format
 * @param format - The format to use (default: 'LL')
 * @returns The formatted date string
 */
export const formatDate = (dateString?: string, format: string = 'DD/MM/YYYY'): string => {
  if (!dateString) return '';
  return moment(dateString).format(format);
};
