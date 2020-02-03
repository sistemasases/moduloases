define([
    'block_ases/vendor-vue',
    'block_ases/grader-utils',
    'core/config',
], function (Vue, g_utils,  cfg) {
    var COURSE_ID = g_utils.getCourseId();
    var BASE_URL = `${cfg.wwwroot}/blocks/ases/managers/customgrader/customgrader_api.php`;
    var api_service = {
        get: (resource) => {
            return Vue.http.get(`${BASE_URL}/${resource}`)
                .then (response => response.body)
                .catch(response => console.error(response))
        } ,
        delete: (resource) => {
            return Vue.http.delete(`${BASE_URL}/${resource}`)
                .then (response => response.body)
                .catch(response => console.error(response))
        },
        post: (data) => {
            const data_ = {
                ...data,
                course: COURSE_ID
            };
            return Vue.http.post(`${BASE_URL}`, data_)
                .then( response=> response.body )
                .catch(response => console.error(response));
        },
        put: (data) => {
            const data_ = {
                ...data,
                course: COURSE_ID
            };
            return Vue.http.put(`${BASE_URL}`, data_)
                .then( response=> response.body )
                .catch(response => console.error(response));
        }
    };
    return {
        get_grader_data: (courseId) => {
            const send_info = {function: "get_grader_data", courseid: courseId};
            return api_service.post(send_info);
        },

        update_grade: (grade, courseId) => {
            const send_info = {function: "update_grade", ...grade, courseid: courseId};
            return api_service.post(send_info);
        },
        update_category: (category) => {
            const send_info = {function: "update_category", category };
            return api_service.post(send_info);
        },
        update_item: (item) => {
            const send_info = {function: "update_item", item };
            return api_service.post(send_info);
        },

        add_category: (category, weight) => {
            const send_info = {function: "add_category", category,  weight};
            return api_service.post(send_info);
        },
        add_item: (item) => {
            const send_info = {function: "add_item", item: item };
            return api_service.post(send_info);
        },
        add_partial_exam: (partial_exam) => {
            const send_info = {function: "add_partial_exam", partial_exam };
            return api_service.post(send_info)
        },

        delete_item: (itemId) => {
            const send_info = {function: "delete_item", itemId: itemId};
            return api_service.post(send_info);
        },
        delete_category: (categoryId) => {
            const send_info = {function: "delete_category", categoryId: categoryId};
            return api_service.post(send_info);
        }
    };
});