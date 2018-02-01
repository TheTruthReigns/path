angular
    .module('app.main.admin', [])
    .factory('AdminService', function($http) {
        return {
            getStudent: function() {
                var query = $http.get('http://unicomhk.net/path2/php/admin.php?action=getStudent');
                return query;
            },
            getMentor: function() {
                var query = $http.get('http://unicomhk.net/path2/php/admin.php?action=getMentor');
                return query;
            },
            getCase: function() {
                var query = $http.get('http://unicomhk.net/path2/php/admin.php?action=getCase');
                return query;
            },
            getView: function() {
                var query = $http.get('http://unicomhk.net/path2/php/admin.php?action=getView');
                return query;
            },
            getLog: function() {
                var query = $http.get('http://unicomhk.net/path2/php/admin.php?action=getLog');
                return query;
            },
            getAllFunction: function(userGroupID) {
                var query = $http.get('http://unicomhk.net/path2/php/admin.php?action=getAllFunction&userGroupID=' + userGroupID);
                return query;
            },

            getActivity: function(){
                var query = $http.get('http://unicomhk.net/path2/php/admin.php?action=getActivity');
                return query;
            },

            update_S: function(userRoleID, boolean) {
                $http.get('http://unicomhk.net/path2/php/admin.php?action=update_S&userRoleID=' + userRoleID + '&boolean=' + boolean);
            },

            update_M: function(userRoleID, boolean) {
                $http.get('http://unicomhk.net/path2/php/admin.php?action=update_M&userRoleID=' + userRoleID + '&boolean=' + boolean);
            },
            getSchoolList: function() {
                var query = $http.get('http://unicomhk.net/path2/php/admin.php?action=getSchoolList');
                return query;
            },
            setSchool: function(school) {
                $http.get('http://unicomhk.net/path2/php/admin.php?action=setSchool&schoolID=' + school.schoolID + '&schoolName=' + school.schoolName);
            },
            getProgramList: function(school) {
                var query = $http.get('http://unicomhk.net/path2/php/admin.php?action=getProgramList');
                return query;
            },
            getEventList: function(school) {
                var query = $http.get('http://unicomhk.net/path2/php/admin.php?action=getEventList');
                return query;
            },
            getProgramTypeList: function(school) {
                var query = $http.get('http://unicomhk.net/path2/php/admin.php?action=getProgramTypeList');
                return query;
            },
            setProgram: function(schoolID, program) {
                $http.get('http://unicomhk.net/path2/php/admin.php?action=setProgram&schoolID=' + schoolID + '&programName=' + program.programName + '&programID=' + program.programID + '&programTypeID=' + program.programTypeID);
            },
            removeEvent: function(event) {
                $http.get('http://unicomhk.net/path2/php/admin.php?action=removeEvent&eventID=' + event.eventID)
            },
            setEvent: function(event) {
                $http.get('http://unicomhk.net/path2/php/admin.php?action=setEvent&eventID=' + event.eventID + '&eventDate=' + event.eventDate + '&eventDetail=' + event.eventDetail + '&eventName=' + event.eventName);
            },
            createEvent: function(event) {
                $http.get('http://unicomhk.net/path2/php/admin.php?action=createEvent&eventDate=' + event.eventDate + ' &eventDetail=' + event.eventDetail + ' &eventName=' + event.eventName);
            },
            createActivity: function(subject, numOfPe, startDate, endDate, content, location, school){
                $http.get('http://unicomhk.net/path2/php/admin.php?action=createActivity&subject=' + subject + '&numOfPe=' + numOfPe + '&startDate=' + startDate + '&endDate=' + endDate + '&content=' + content + '&location=' + location + '&school=' + school);
             },
            updateActivity: function(subject, numOfPe, startDate, endDate, content, location, id, school){
                $http.get('http://unicomhk.net/path2/php/admin.php?action=updateActivity&subject=' + subject + '&numOfPe=' + numOfPe + '&startDate=' + startDate + '&endDate=' + endDate + '&content=' + content + '&location=' + location + '&id=' + id + '&school=' + school);
             },
             deleteActivity: function(id){
                $http.get('http://unicomhk.net/path2/php/admin.php?action=deleteActivity&id=' + id);
             }, 

        };
    });
