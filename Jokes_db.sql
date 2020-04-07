/*
** @Author Morel Sami
**
*/


/*
 * create required database `Jokes` for the project
 */

  create database IF NOT EXISTS Jokes;
  commit;

  /*
  * Drop all tables in the database
  */

  
  drop table IF EXISTS Rating;
  drop table IF EXISTS Comments;
  drop table IF EXISTS Jokes;
  drop table IF EXISTS Users;
  commit;


 /*create all required tables*/
create table IF NOT EXISTS  Users (userId int(11) auto_increment, username varchar(30), upass VARCHAR(30), primary key(userId));
create table  IF NOT EXISTS Jokes (jokeId varchar(50), joke mediumtext, total_rate_value int(20), num_of_ratings int(20), 
AvgRating decimal(6,2), primary key(jokeId)); 
create table IF NOT EXISTS  Comments (commentId int(11) auto_increment, userId int(11), jokeId varchar(50), comment mediumtext, date DATETIME, primary key(CommentId), foreign key (userID) references 
	Users(userId), foreign key (jokeId) references Jokes(jokeId));
create table IF NOT EXISTS  Rating (rateId int(11) auto_increment, jokeId varchar(50), userId int(11), rate int(3), primary key(rateId, jokeId, userId), foreign key (jokeId) references 
	Jokes(jokeId), foreign key (userId) references Users(userId));
commit;

/*
 * parse in raw data into tables for testing
 */
Insert into Users(username, upass) values('mksami237@gmail.com', 'smorel237'); 
commit;

/*Insert into Rating(jokeId,userId,rate) values ("0189hNRf2g", 1, 3);
Insert into Rating(jokeId,userId,rate) values ("a29pbp4haFd",1,5);
Insert into Rating(jokeId,userId,rate) values ("sHlqrjyPf", 1,1);	
Insert into Rating(jokeId,userId,rate) values ("SnOf2gqjiqc",1,4); 	
Insert into Rating(jokeId,userId,rate) values ("sPfqWDlq4Ed", 1,5);	
Insert into Rating(jokeId,userId,rate) values ("TCY0LmGQnb", 1,3);
commit;*/

/*
 *  use the sql statements below after fetching 
 * all the jokes from the host api to use for testing
 */
/*
update Jokes set AvgRating = 2.3 where jokeId= "0189hNRf2g";
update Jokes set AvgRating = 5.3 where jokeId= "a29pbp4haFd";
update Jokes set AvgRating = 1.0 where jokeId= "sHlqrjyPf";
update Jokes set AvgRating = 10.3 where jokeId= "SnOf2gqjiqc";
update Jokes set AvgRating = 6.5 where jokeId= "sPfqWDlq4Ed";
update Jokes set AvgRating = 2.3 where jokeId= "TCY0LmGQnb";
commit;
*/


