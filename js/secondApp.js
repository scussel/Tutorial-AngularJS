var myApp = angular.module('angularApp', []);

myApp.controller("TodoListController", function($http){
	var todoList = this;
	var request;

	/*todoList.todos = [
		{ id:1, text:'Teste com todolist no angular', datetime:'26/02/2016 14:43:12', done:false },
		{ id:2, text:'Teste do app em funcionamento', datetime:'28/02/2016 19:01:44', done:false },
		{ id:3, text:'Fazer testes com DB', datetime:'28/02/2016 21:30:00', done:false }
	];*/

	request = $http({
		method: "post",
		url: "secondTutorialFunction.php",
		data: {
			function: 'getAllTodos',
			status: '0',
			archived: '0'
		},
		headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
	});

	request.success(function (data) {
		todoList.todos = data.records;
		console.log(data);
	});

	todoList.addTodo = function() {
		var oldTodos = todoList.todos;
		var maxId = 0;

		request = $http({
			method: "post",
			url: "secondTutorialFunction.php",
			data: {
				function: 'addTodo',
				text: todoList.todoText
			},
			headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
		});

		request.success(function (data) {
			//if(data.error == ''){
				todoList.todos.push(
					{id:data.id, text:data.text, datetime:data.date_time, done:false}
				);
				todoList.todoText = '';
			/*}else{
				alert(data.error);
			}*/
		});
	};

	todoList.remaining = function() {
		var count = 0;
		angular.forEach(todoList.todos, function(todo) {
			count += todo.done ? 0 : 1;
		});
		return count;
	};

	todoList.archive = function() {
		var oldTodos = todoList.todos;
		todoList.todos = [];
		angular.forEach(oldTodos, function(todo) {
			if(!todo.done){
				todoList.todos.push(todo);
			}else{
				request = $http({
					method: "post",
					url: "secondTutorialFunction.php",
					data: {
						function: 'archiveTodos',
						id: todo.id
					},
					headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
				});
			}
		});
	};

	todoList.select = function(index) {
		todoList.selected = index; 
	};
});
