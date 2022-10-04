import {DATE_FORMAT, DATETIME_DB_FORMAT, DATETIME_FORMAT} from '@/core/constants/time.constants'
import moment from 'moment'

export default {
    data: () => ({
        globalPageSize: 10,
    }),
    methods: {
        GlobalFormatDate(value) {
            return moment(value).format('DD.MM.YYYY hh:mm')
        }
    },
    filters: {
        capitalize: function (value) {
            if (!value) return ''
            value = value.toString()
            return value.charAt(0).toUpperCase() + value.slice(1)
        },
        price: function (number) {
            return Number(number).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '\'')
        },
        date: function (datetime) {
            return dayjs(datetime).isValid() ? dayjs(datetime, DATETIME_DB_FORMAT).format(DATE_FORMAT) : ''
        },
        time: function (datetime) {
            return dayjs(datetime).isValid() ? dayjs(datetime, DATETIME_DB_FORMAT).format(DATETIME_FORMAT) : ''
        },
    },
}
