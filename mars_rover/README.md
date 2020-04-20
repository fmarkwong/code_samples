# Mars Rover Simulation 


## Requirements
A squad of robotic rovers are to be landed by NASA on a plateau on Mars. This plateau, which is curiously rectangular, must be navigated by the rovers so that their on-board cameras can get a complete view of the surrounding terrain to send back to Earth.

A rover's position and location is represented by a combination of x and y co-ordinates and a letter representing one of the four cardinal compass points. The plateau is divided up into a grid to simplify navigation. An example position might be 0, 0, N, which means the rover is in the bottom left corner and facing North.

In order to control a rover, NASA sends a simple string of letters. The possible letters are 'L', 'R' and 'M'. 'L' and 'R' makes the rover spin 90 degrees left or right respectively, without moving from its current spot. 'M' means move forward one grid point, and maintain the same heading.

Assume that the square directly North from (x, y) is (x, y+1).

INPUT:
The first line of input is the upper-right coordinates of the plateau, the lower-left coordinates are assumed to be 0,0.

The rest of the input is information pertaining to the rovers that have been deployed. Each rover has two lines of input. The first line gives the rover's position, and the second line is a series of instructions telling the rover how to explore the plateau.

The position is made up of two integers and a letter separated by spaces, corresponding to the x and y co-ordinates and the rover's orientation.

Each rover will be finished sequentially, which means that the second rover won't start to move until the first one has finished moving.


OUTPUT
The output for each rover should be its final co-ordinates and heading. 

INPUT AND OUTPUT

Test Input:
5 5
1 2 N
LMLMLMLMM
3 3 E
MMRMMRMRRM

Expected Output:
1 3 N
5 1 E



## Details

Instructions:
  To run application:

    - Copy files to a directory
    - make main.rb executable
      - 'chmod +x main.rb'
    - run main.rb
      - './main.rb'

To run tests:

`rspec -c -fd tests/mars_rover_spec.rb`

Design:

I tried to design this application in a way that is loosely based on a simplified domain driven design philosophy.  My goal was to have as close as possible a one to one mapping of my objects to the real world of NASA rovers (or at least what I imagine it to be).  I believe that this makes code easier to understand because it is easier to make the connection between real world requirements and the code when names of objects and methods actually reflects the real world.  And in general, I usually try to avoid too many layers of abstraction, only what's necessary.  When trying to understand others' code, it's frustrating trying to trace through too many layers.  Of course, with large projects, sometimes this is not easily avoided, but it's I think it's good genral guideline to follow.

main.rb is the main driver program.

mission_control.rb gives the top level overview of the overall design of the software. Each method in that class reflects functionality that I imagine would really be part of a rover space program.

The mission data is read by the MissionDataReader object.  I made this class to accept either a file or string as input.  That way, testing is a lot easier as I can pass in various string inputs for the unit tests instead of reading different versions of the data from files. The file or string is parsed into an array representing each line of the mission data input.

PlateauData is a class that stores size information of the plateau. 

RoverInstructions stores the initial position data (where the rover will first land on the plateau) and the motion instructions.

Since the number of Rovers is determined by the mission data, the MissionControl#initialize_rovers method loops over the mission data (two lines at a time), grabs the rover instructions and instiantiates the Rovers.

Each Rover object takes the RoverInstructions and PlateauData and represents the Rover. It's main functionalities are rotating right and left and moving forward.  The PlateauData lets the rover know the size of the plateau.  The RoverInstructions tell the rover which direction to turn and how much to move forward. There are constraints built in so when a rover get to the edge of the plateau, it is prevented from moving forward another step. The rover keeps track of x and y location coordinates and current direction its pointing.

Direction is a collaborator object of Rover.  It stores the current direction of the rover and handles rotating left and right. I felt that the logic for this was complex enough to warrant its own class.

Once each rover has been loaded with it's instructions and plateau data, MissionControl@execute_rover_missions has each rover land on the plateau and execute its motions.  The method also keeps track of the final position and direction of each rover and stores it in the mission log which is displayed at the end.

I made no assumptions about the mission data input, specifically, whether it would be in the correct format.  So every object that holds and parses mission data has built in data validation methods. At first I thought to stores these validators in one Validation class, but then I opted to build the validators into each respective class.  I found that this way made for cleaner code.  The Validation class way requires the programmer to remember to call the validation methods for each piece of data at the right place.  By baking the validation into each class, the client programmer does not have to think about validation.  As each object is instantiated, validation kicks in and if invalid data is encountered, the appropriate exception/warning message is raised and depending on the severity of the error, the mission is aborted, or the data is skipped. Input validation is automatic and seamless. 

Most of the data validation and functionality above with various edge cases is covered in unit tests in tests/mars_rover_spec.rb 
