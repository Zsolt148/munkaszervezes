export function flash(that, resp) {
    if (resp.success) {
        that.$page.props.flash.success = resp.success
    } else if (resp.error) {
        that.$page.props.flash.error = resp.error
    }
}