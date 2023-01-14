const dayjs = require("dayjs");
let relativeTime = require("dayjs/plugin/relativeTime");
let localizedFormat = require("dayjs/plugin/localizedFormat");
dayjs.extend(localizedFormat);
dayjs.extend(relativeTime);

export function toLocalizedFormat(date, format = "L LT") {
    return dayjs(date).format(format);
}

export function toRelativeTime(date) {
    return dayjs().to(dayjs(date));
}
