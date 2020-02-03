define([
    'block_ases/grader-utils',
    'block_ases/grader-enums',
    'block_ases/grader-service',
    'block_ases/vendor-vue',
    'block_ases/vendor-lodash'
], function (g_utils, g_enums, g_service, Vue, _) {

    const columnFinalGrade = {text: "Nota final"};
    const columnStudentCode= {text: "Código estudiante"};
    const columnStudentNames= {text: "", hide: true};


    /**
     * Return an array of students sorted
     * @param sortStudentMethodType
     * @param students {Array<Student>}
     * @returns {function(*, *): boolean}
     */
    var sortStudents = function (students, sortStudentMethodType) {
        switch (sortStudentMethodType.name) {
            case g_enums.sortStudentMethods.FIRST_NAME:
                return _.orderBy(students, ['firstname'], sortStudentMethodType.order);
            case g_enums.sortStudentMethods.LAST_NAME:
                return _.orderBy(students, ['lastname'], sortStudentMethodType.order);
        }
    };
    var mutationsType = {
        ADD_GRADE: 'addGrade',
        DELETE_CATEGORY: 'deleteCategory',
        ADD_ITEM: 'addItem',
        ADD_CATEGORY: 'addCategory',
        ADD_GRADE_TO_STUDENT: 'addGradeToStudent',
        SET_STATE: 'setAllState',
        SET_GRADE: 'setGrade',
        SET_GRADES: 'setGrades',
        SET_CATEGORY: 'setCategory',
        SET_STUDENT_SORT_METHOD: 'setStudentSortMethod',
        SET_SELECTED_CATEGORY_ID: 'setSelectedCategoryId',
        SET_LEVELS: 'setLevels',
        SET_ITEM: 'setItem',
        DELETE_ITEM: 'deleteItem',
        DELETE_GRADE: 'deleteItemGrades',
    };
    var actionsType = {
        FETCH_STATE: 'fetchAllState',
        FILL_GRADES: 'fillGrades',
        FILL_GRADES_FOR_NEW_ITEM: 'fillGradesForNewItem',
        UPDATE_GRADE: 'updateGrade',
        DELETE_ITEM: 'deleteItem',
        DELETE_CATEGORY_CHILDS: 'deleteCategoryChilds',
        UPDATE_CATEGORY: 'setCategory',
        ADD_ITEM: 'addItem',
        ADD_PARTIAL_EXAM: 'addPartialExam',
        DELETE_CATEGORY: 'deleteCategory',
        ADD_CATEGORY: 'addCategory',
        UPDATE_ITEM: 'setItem',
        DELETE_ITEM_GRADES: 'deleteItemGrades'
    };
    var store = {

        state : {
            decimalPlaces: 2,
            additionalColumnsAtFirst: [
                columnStudentCode,
                columnStudentNames
            ],
            additionalColumnsAtEnd: [
                columnFinalGrade
            ],
            sortStudentsMethodType: {
                name: g_enums.sortStudentMethods.LAST_NAME,
                order: g_enums.sortDirection.ASC
            },
            students /*: Dict<studentId: Student> */: {},
            selectedCategoryId: null,
            items /*: Dict<itemId:Item> */: {},
            categories /*: Array<Category> */: [],
            grades /*: Dict<gradeId:Grade> */: {},
            levels: [], // First level is course level, last level is item level, between
            //this two levels are category levels
            course: {fullname: 'Nombre completo de el curso'},
            maxDisplayGrade: 5, //Used as a scale for the graphs
            gradeDisplayRange: 0.5, //Used to divide the scale in the line graph
        },

        mutations: {
            [mutationsType.DELETE_ITEM] (state, itemId) {
                Vue.delete(state.items, itemId);
            },
            [mutationsType.DELETE_CATEGORY] (state, categoryId) {
                const categoryIndex = state.categories
                    .map(category => category.id)
                    .indexOf(categoryId);
                Vue.delete(state.categories, categoryIndex);
            },
            [mutationsType.SET_STUDENT_SORT_METHOD](state, sortMethodType) {
                state.sortStudentsMethodType = sortMethodType;
            },
            [mutationsType.DELETE_GRADE] (state, gradeId) {
                Object.keys(state.students).forEach( studentId => {
                        const student = state.students[studentId];
                        Vue.set(
                            state.students[studentId],
                            'gradeIds',
                            student.gradeIds.filter(_gradeId => _gradeId !== gradeId)
                        )
                    }
                );
                Vue.delete(state.grades, gradeId);
            },
            [mutationsType.ADD_ITEM](state, item) {
                Vue.set(state.items, item.id, item);
            },
            [mutationsType.ADD_CATEGORY](state, category) {
                state.categories.push(category);
            },
            [mutationsType.ADD_GRADE] (state, grade) {
                grade.id = g_utils.ID();
                let student = state.students[grade.userid];
                let studentGradeIds = student.gradeIds? student.gradeIds: [];
                Vue.set(state.grades, grade.id, grade);
                Vue.set(state.students[student.id], 'gradeIds', [...studentGradeIds, grade.id]);
            },
            [mutationsType.ADD_GRADE_TO_STUDENT] (state, payload) {
                let grade = payload.grade;
                let studentId = payload.studentId;
                let student = state.students[studentId];
                let studentGradeIds = student.gradeIds? student.gradeIds: [];
                Vue.set(state.students[studentId], 'gradeIds', [...studentGradeIds, grade.id]);
            },
            [mutationsType.SET_ITEM] (state, newItem) {
                Vue.set(state.items, newItem.id, newItem);
            },
            [mutationsType.SET_LEVELS] (state, levels) {
                state.levels = levels;
            },
            [mutationsType.SET_CATEGORY] (state, newCategory) {
                let category_index = state.categories.map(category => category.id).indexOf(newCategory.id);
                Vue.set(state.categories, category_index, newCategory);
            },
            [mutationsType.SET_GRADES] (state, newGrades) {
                newGrades.forEach(newGrade => {
                    newGrade.finalgrade = g_utils.removeInsignificantTrailZeros(newGrade.finalgrade);

                    if(!state.grades[newGrade.id]) {
                        const oldGrade = Object.values(state.grades).find(grade =>
                            grade.itemid === newGrade.itemid &&
                            grade.userid === newGrade.userid);
                        state.grades[newGrade.id] =  newGrade;
                        const studentGradeIds = state.students[oldGrade.userid].gradeIds;
                        const newGradeIds =
                            [...studentGradeIds.filter(gradeId => gradeId !== oldGrade.id), newGrade.id];
                        state.students[newGrade.userid] = {...state.students[newGrade.userid], gradeIds: newGradeIds};
                        Vue.delete(state.grades, oldGrade.id);
                    }
                    Vue.set(state.grades, newGrade.id, newGrade);
              })  ;
            },
            [mutationsType.SET_GRADE] (state, payload) {
                let oldGrade = payload.old;
                let newGrade = payload.new;
                newGrade.finalgrade = g_utils.removeInsignificantTrailZeros(newGrade.finalgrade);
                state.grades[newGrade.id] = newGrade;
                if( oldGrade ) {
                    if (oldGrade.id !== newGrade.id) {
                        const studentGradeIds = state.students[oldGrade.userid].gradeIds;
                        const newGradeIds =
                            [...studentGradeIds.filter(gradeId => gradeId !== oldGrade.id), newGrade.id];
                        state.students[oldGrade.userid] = {...state.students[oldGrade.userid], gradeIds: newGradeIds};
                        Vue.delete(state.grades, oldGrade.id);
                    }
                }
            },
            [mutationsType.SET_SELECTED_CATEGORY_ID] (state, newSelectedId) {
                state.selectedCategoryId = newSelectedId;
            },
            [mutationsType.SET_STATE] (state, newState) {
                state.levels = newState.levels;
                let studentsDict = {};
                newState.students.forEach(student => {
                    studentsDict[student.id] = student;
                });
                state.students = studentsDict;
                let itemsDict = {};
                newState.items.forEach(item => {
                    itemsDict[item.id] = item;
                });
                state.items = itemsDict;
                state.categories = newState.categories;
                let gradesDict = {};
                newState.grades.forEach(grade => {
                    gradesDict[grade.id] = {...grade, finalgrade: g_utils.removeInsignificantTrailZeros(grade.finalgrade)};
                });
                state.grades = gradesDict;
                state.course = newState.course;
            }
        },
        actions: {
            [actionsType.DELETE_ITEM] ({commit, dispatch, state}, itemId) {
                g_service.delete_item(itemId)
                    .then( response => {
                        commit(mutationsType.SET_LEVELS, response.levels);
                        commit(mutationsType.DELETE_ITEM, itemId);
                        dispatch(actionsType.DELETE_ITEM_GRADES, itemId);
                    });
            },
            [actionsType.ADD_ITEM] ({commit, dispatch}, item) {
              g_service.add_item(item)
                  .then(response => {
                      commit(mutationsType.ADD_ITEM, response.item);
                      commit(mutationsType.SET_LEVELS, response.levels);
                      dispatch(actionsType.FILL_GRADES_FOR_NEW_ITEM, response.item);
                  });
            },
            [actionsType.DELETE_ITEM_GRADES]({commit, state}, itemId) {
                let gradeIds = Object.keys(state.grades);
                let gradeIdsToDelete = [];
                gradeIds.forEach(gradeId => {
                    if(state.grades[gradeId].itemid === itemId) {
                        gradeIdsToDelete.push(gradeId);
                    }
                });
                gradeIdsToDelete.forEach(gradeId => {
                    commit(mutationsType.DELETE_GRADE, gradeId);
                });
            },
            [actionsType.ADD_PARTIAL_EXAM] ({commit, getters}, partialExam) {
              g_service.add_partial_exam(partialExam)
                  .then(response => {
                      commit(mutationsType.SET_LEVELS, response.levels);
                      commit(mutationsType.ADD_CATEGORY, response.category);
                      commit(mutationsType.ADD_ITEM, response.partial_item);
                      commit(mutationsType.ADD_ITEM, response.optional_item);
                  });
            },
            [actionsType.DELETE_CATEGORY_CHILDS] ({commit, getters, dispatch}, categoryId) {
                const childItems = getters.categoryChildItems(categoryId);
                const childCategories = getters.categoryChildCategories(categoryId);
                childItems.forEach(item => {
                    commit(mutationsType.DELETE_ITEM, item.id);
                    dispatch(actionsType.DELETE_ITEM_GRADES, item.id);
                });
                childCategories.forEach(category => {
                   commit(mutationsType.DELETE_CATEGORY, category.id);
                });

            },
            [actionsType.DELETE_CATEGORY] ({commit, getters, dispatch}, categoryId) {
                g_service.delete_category(categoryId)
                    .then(response => {
                        commit(mutationsType.SET_LEVELS, response.levels);
                        dispatch(actionsType.DELETE_CATEGORY_CHILDS, categoryId);
                        commit(mutationsType.DELETE_CATEGORY, categoryId);

                    })
            },
            [actionsType.FILL_GRADES_FOR_NEW_ITEM] ({commit, state, getters}, item) {
                let studentIds = Object.keys(state.students);
                studentIds.forEach(studentId => {
                    let grade = {
                        userid: studentId,
                        itemid: item.id,
                        finalgrade: null,
                        rawgrademin: item.grademin,
                        rawgrademax: item.grademax
                    };
                    commit(mutationsType.ADD_GRADE, grade);
                });
            },
            /**
             * When the grades are retrieved by the backend, only the grades graded are returned,
             * items without grades are no returned, in the interface we need all grades for
             * each student in each item, if the item is not graded, a fake grade is created and added
             * in `grades` and `studentGradeIds`
             * @param commit
             * @param state
             * @param getters
             */
            [actionsType.FILL_GRADES] ({ commit, state, getters }) {
                let studentIds = Object.keys(state.students);
                let grades = Object.values(state.grades);
                studentIds.forEach(studentId => {
                    for(var itemId of getters.itemOrderIds /* The grades are printed in this order*/) {
                        let item = state.items[itemId];
                        let gradeResult = grades.find(grade => grade.userid === studentId && grade.itemid === item.id);
                        if(!gradeResult) {
                            let grade = {
                                userid: studentId,
                                itemid: item.id,
                                finalgrade: null,
                                rawgrademin: item.grademin,
                                rawgrademax: item.grademax
                            };
                            commit(mutationsType.ADD_GRADE, grade);
                        } else {
                            commit(mutationsType.ADD_GRADE_TO_STUDENT, {studentId: studentId, grade: gradeResult} );
                        }
                    }

                }) ;
            },
            [actionsType.UPDATE_GRADE] ({commit, state}, grade) {
                g_service.update_grade(grade, state.course.id)
                    .then( response => {
                        commit(mutationsType.SET_GRADE, {old: grade, new: response.grade});
                        commit(mutationsType.SET_GRADES, response.other_grades);
                    });
            },
            [actionsType.UPDATE_CATEGORY]({dispatch, commit}, category) {
                g_service.update_category(category)
                    .then( response => {
                        let category = response.category;
                        let levels = response.levels;
                        commit(mutationsType.SET_CATEGORY, category);
                        commit(mutationsType.SET_LEVELS, levels);
                    })
            },
            [actionsType.ADD_CATEGORY] ({commit, state, dispatch}, payload) {
                g_service.add_category(payload.category, payload.weight)
                    .then( response =>  {
                        let category = response.category;
                        let category_item = response.category_item;
                        let levels = response.levels;
                        commit(mutationsType.ADD_ITEM, category_item);
                        commit(mutationsType.ADD_CATEGORY, category);
                        commit(mutationsType.SET_LEVELS, levels);
                        dispatch(actionsType.FILL_GRADES_FOR_NEW_ITEM, category_item)
                    });
            },
            [actionsType.UPDATE_ITEM]({dispatch, commit}, item) {
                g_service.update_item(item)
                    .then( response => {
                        let item = response.item;
                        let levels = response.levels;
                        commit(mutationsType.SET_ITEM, item);
                        commit(mutationsType.SET_LEVELS, levels);
                    })
            },
            [actionsType.FETCH_STATE] ({dispatch, commit}) {
                g_service.get_grader_data(g_utils.getCourseId())
                    .then( response => {
                        commit(mutationsType.SET_STATE, response);
                        dispatch(actionsType.FILL_GRADES);
                    })
            }
        },
        getters: {
            courseLevel: (state) => {
                return state.levels[0]?state.levels[0][0]: [];
            },
            selectedCategory: (state, getters) => {
                return getters.categoryById(state.selectedCategoryId);
            },
            itemLevel:(state) => {
                return state.levels[state.levels.length-1];
            },
            categoryLevels: (state) => {
                let slice =  state.levels.slice(1, state.levels.length -1 );
                return slice? slice: [];
            },
            itemsCount: (state) => {
                return Object.keys(state.items).length;
            },
            categoryById: (state) => (id) => {
                return  state.categories.find (category => category.id === id);
            },
            studentById: (state) => (id) => {
                return  state.students[id];
            },
            studentSetSorted: (state, getters) => {
                return  sortStudents(getters.studentSet, state.sortStudentsMethodType);
            },
            studentSet: (state) => {
                return Object.values(state.students);
            },
            studentsCount: (state) => {
                return Object.keys(state.students).length;
            },
            studentsAsesCount: (state, getters) => {
                return getters.studentSet.filter(student => student.is_ases).length;
            },
            itemSet: (state) => {
                return Object.values(state.items);
            },
            /**
             * Use this getter newGradenewGrader item set when you should show
             * or manage items ordered, in the same order than should
             * have for the table
             * @param state
             * @param getters
             * @returns {*}
             */
            itemOrderIds: (state, getters) => {
                let itemLevel = getters.itemLevel; //see itemLevel function in getters
                if(!itemLevel) {
                    return Object.keys(state.items);
                }
                return itemLevel.map(element => element.object.id);
            },
            itemOrderedNames: (state, getters) => {
                let itemLevel = getters.itemLevel; //see itemLevel function in getters
                if(!itemLevel) {
                    return Object.keys(state.items);
                }
                return itemLevel.map(element => {
                    if(element.object.itemname === null || element.object.itemname === ''){
                        return 'Total ' + getters.categoryById(element.object.iteminstance).fullname;
                    }else return element.object.itemname;
                });
            },
            finalGradeId: (state, getters) => {
                return getters.itemOrderIds[getters.itemOrderIds.length - 1];
            },
            categoryChildItems: (state, getters) => (idCategory) => {
                let children =  getters.itemSet.filter(item => {
                        return item.categoryid === idCategory ||
                            item.iteminstance === idCategory //
                    }
                );
                if(Array.isArray(children)) {
                    return children;
                } else {
                    return [];
                }
            },
            categoryChildCategories: (state) => (idCategory) => {
                let children =  state.categories.filter(category => category.parent === idCategory);
                return children? children: [];
            },
            categoryChildSize: (state, getters) => (idCategory) => {

                let categoryChildItems = getters.categoryChildItems(idCategory);
                let categoryChildCategories = getters.categoryChildCategories(idCategory);
                return categoryChildItems.length + categoryChildCategories.length;
            },
            categoryDepth: (state) => {
                if(state.categories.length <= 0) {
                    return 0;
                }
                var depths =  state.categories.map(category => { return category.depth; });
                return Math.max.apply(Math,depths);

            },
            getCategoriesByDepth: (state) => (depth) => {
                return state.categories.find(category=>category.depth === depth);
            },
            gradesSet: (state) => {
                return Object.values(state.grades);
            },
            gradesByItemId: (state, getters) => (id) => {
                return getters.gradesSet.filter(grade => grade.itemid === id);
            },
            passingGrades: (state, getters) => {
                return getters.gradesSet.filter(grade => grade.finalgrade >= grade.rawgrademax*0.6);
            },
            passingGradesSet: (state, getters) => {
                return getters.itemOrderIds.map(id => {
                    return getters.gradesByItemId(id).filter(grade => grade.finalgrade >= grade.rawgrademax*0.6).length;
                });
            },
            failingGrades: (state, getters) => {
                return getters.gradesSet.filter(grade => grade.finalgrade < grade.rawgrademax*0.6 && grade.finalgrade != null);
            },
            failingGradesSet: (state, getters) => {
                return getters.itemOrderIds.map(id => {
                    return getters.gradesByItemId(id).filter(grade => {
                        if(grade.finalgrade < grade.rawgrademax*0.6 && grade.finalgrade != null){
                            return true;
                        }else return false
                    }).length;
                });
            },
            nullGrades: (state, getters) => {
                return getters.gradesSet.filter(grade => grade.finalgrade === null || grade.finalgrade === undefined);
            },
            nullGradesSet: (state, getters) => {
                return getters.itemOrderIds.map(id => {
                    return getters.gradesByItemId(id).filter(grade => grade.finalgrade === null || grade.finalgrade === undefined).length;
                });
            },
            passingGradesCount: (state, getters) => {
                return getters.passingGrades.length;
            },
            failingGradesCount: (state, getters) => {
                return getters.failingGrades.length;
            },
            nullGradesCount: (state, getters) => {
                return getters.nullGrades.length;
            },
            finalGradesSet: (state, getters) => {
                return getters.gradesSet.filter(grade => grade.itemid === getters.finalGradeId);
            },
            finalPassingGradeSet: (state, getters) => {
                return getters.finalGradesSet.filter(grade => grade.finalgrade >= grade.rawgrademax*0.6);
            },
            lineGraphLabel: (state) => {
                let label = [];
                let number = state.maxDisplayGrade/state.gradeDisplayRange;
                let left = 0;
                let range = '';
                for(i = 0; i < number - 1; i++){
                    range = left + " - ";
                    left += state.gradeDisplayRange;
                    range += left - 0.1;
                    label.push(range);
                }
                range = left + " - " + state.maxDisplayGrade;
                label.push(range);
                return label;
            },
            getGradesByRange: (state, getters) => {
                let lastMax = 0;
                let currentMax = state.gradeDisplayRange;
                let data = [];
                let number = state.maxDisplayGrade/state.gradeDisplayRange;
                let weightedGrades = getters.finalGradesSet.map(grade => (grade.finalgrade/grade.rawgrademax)*state.maxDisplayGrade);
                for(i = 0; i < number - 1; i++){
                    data.push(weightedGrades.filter(grade => grade >= lastMax && grade < currentMax).length);
                    lastMax += state.gradeDisplayRange;
                    currentMax += state.gradeDisplayRange;
                }
                data.push(weightedGrades.filter(grade => grade >= lastMax).length);
                return data;
            }
        }
    };

    return {
        store: store,
        mutations: mutationsType,
        actions: actionsType
    }
});