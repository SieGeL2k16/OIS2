<?php
/**
 * Extension: Show Process details.
 * List of all command types for the "COMMAND" column in V$SESSION view.
 * @package OIS2
 * @author Sascha 'SieGeL' Pfalz <php@saschapfalz.de>
 * @version 2.01 (12-Sep-2011)
 * $Id$
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
$cmdarray = array();

$cmdarray[0]  = "UNKNOWN CODE!";
$cmdarray[1]  = "CREATE TABLE";
$cmdarray[2]  = "INSERT";
$cmdarray[3]  = "SELECT";
$cmdarray[4]  = "CREATE CLUSTER";
$cmdarray[5]  = "ALTER CLUSTER";
$cmdarray[6]  = "UPDATE";
$cmdarray[7]  = "DELETE";
$cmdarray[8]  = "DROP CLUSTER";
$cmdarray[9]  = "CREATE INDEX";
$cmdarray[10] = "DROP INDEX";
$cmdarray[11] = "ALTER INDEX";
$cmdarray[12] = "DROP TABLE";
$cmdarray[13] = "CREATE SEQUENCE";
$cmdarray[14] = "ALTER SEQUENCE";
$cmdarray[15] = "ALTER TABLE";
$cmdarray[16] = "DROP SEQUENCE";
$cmdarray[17] = "GRANT";
$cmdarray[18] = "REVOKE";
$cmdarray[19] = "CREATE SYNONYM";
$cmdarray[20] = "DROP SYNONYM";
$cmdarray[21] = "CREATE VIEW";
$cmdarray[22] = "DROP VIEW";
$cmdarray[23] = "VALIDATE INDEX";
$cmdarray[24] = "CREATE PROCEDURE";
$cmdarray[25] = "ALTER PROCEDURE";
$cmdarray[26] = "LOCK TABLE";
$cmdarray[27] = "NO OPERATION";
$cmdarray[28] = "RENAME";
$cmdarray[29] = "COMMENT";
$cmdarray[30] = "AUDIT";
$cmdarray[31] = "NOAUDIT";
$cmdarray[32] = "CREATE DATABASE LINK";
$cmdarray[33] = "DROP DATABASE LINK";
$cmdarray[34] = "CREATE DATABASE";
$cmdarray[35] = "ALTER DATABASE";
$cmdarray[36] = "CREATE ROLLBACK SEGMENT";
$cmdarray[37] = "ALTER ROLLBACK SEGMENT";
$cmdarray[38] = "DROP ROLLBACK SEGMENT";
$cmdarray[39] = "CREATE TABLESPACE";
$cmdarray[40] = "ALTER TABLESPACE";
$cmdarray[41] = "DROP TABLESPACE";
$cmdarray[42] = "ALTER SESSION";
$cmdarray[43] = "ALTER USE";
$cmdarray[44] = "COMMIT";
$cmdarray[45] = "ROLLBACK";
$cmdarray[46] = "SAVEPOINT";
$cmdarray[47] = "PL/SQL EXECUTE";
$cmdarray[48] = "SET TRANSACTION";
$cmdarray[49] = "ALTER SYSTEM SWITCH LOG";
$cmdarray[50] = "EXPLAIN";
$cmdarray[51] = "CREATE USER";
$cmdarray[25] = "CREATE ROLE";
$cmdarray[53] = "DROP USER";
$cmdarray[54] = "DROP ROLE";
$cmdarray[55] = "SET ROLE";
$cmdarray[56] = "CREATE SCHEMA";
$cmdarray[57] = "CREATE CONTROL FILE";
$cmdarray[58] = "ALTER TRACING";
$cmdarray[59] = "CREATE TRIGGER";
$cmdarray[60] = "ALTER TRIGGER";
$cmdarray[61] = "DROP TRIGGER";
$cmdarray[62] = "ANALYZE TABLE";
$cmdarray[63] = "ANALYZE INDEX";
$cmdarray[64] = "ANALYZE CLUSTER";
$cmdarray[65] = "CREATE PROFILE";
$cmdarray[66] = "DROP PROFILE";
$cmdarray[67] = "ALTER PROFILE";
$cmdarray[68] = "DROP PROCEDURE";
$cmdarray[69] = "DROP PROCEDURE";
$cmdarray[70] = "ALTER RESOURCE COST";
$cmdarray[71] = "CREATE SNAPSHOT LOG";
$cmdarray[72] = "ALTER SNAPSHOT LOG";
$cmdarray[73] = "DROP SNAPSHOT LOG";
$cmdarray[74] = "CREATE SNAPSHOT";
$cmdarray[75] = "ALTER SNAPSHOT";
$cmdarray[76] = "DROP SNAPSHOT";
$cmdarray[79] = "ALTER ROLE";
$cmdarray[85] = "TRUNCATE TABLE";
$cmdarray[86] = "TRUNCATE CLUSTER";
$cmdarray[88] = "ALTER VIEW";
$cmdarray[91] = "CREATE FUNCTION";
$cmdarray[92] = "ALTER FUNCTION";
$cmdarray[93] = "DROP FUNCTION";
$cmdarray[94] = "CREATE PACKAGE";
$cmdarray[95] = "ALTER PACKAGE";
$cmdarray[96] = "DROP PACKAGE";
$cmdarray[97] = "CREATE PACKAGE BODY";
$cmdarray[98] = "ALTER PACKAGE BODY";
$cmdarray[99] = "DROP PACKAGE BODY";
$cmdarray[100]= "LOGON";
$cmdarray[101]= "LOGOFF";
$cmdarray[102]= "LOGOFF BY CLEANUP";
$cmdarray[103]= "SESSION REC";
$cmdarray[104]= "SYSTEM AUDIT";
$cmdarray[105]= "SYSTEM NOAUDIT";
$cmdarray[106]= "AUDIT DEFAULT";
$cmdarray[107]= "NOAUDIT DEFAULT";
$cmdarray[108]= "SYSTEM GRANT";
$cmdarray[109]= "SYSTEM REVOKE";
$cmdarray[110]= "CREATE PUBLIC SYNONYM";
$cmdarray[111]= "DROP PUBLIC SYNONYM";
$cmdarray[112]= "CREATE PUBLIC DATABASE LINK";
$cmdarray[113]= "DROP PUBLIC DATABASE LINK";
$cmdarray[114]= "GRANT ROLE";
$cmdarray[115]= "REVOKE ROLE";
$cmdarray[116]= "EXECUTE PROCEDURE";
$cmdarray[117]= "USER COMMENT";
$cmdarray[118]= "ENABLE TRIGGER";
$cmdarray[119]= "DISABLE TRIGGER";
$cmdarray[120]= "ENABLE ALL TRIGGERS";
$cmdarray[121]= "DISABLE ALL TRIGGERS";
$cmdarray[122]= "NETWORK ERROR";
$cmdarray[123]= "EXECUTE TYPE";
$cmdarray[157]= "CREATE DIRECTORY";
$cmdarray[158]= "DROP DIRECTORY";
$cmdarray[159]= "CREATE LIBRARY";
$cmdarray[160]= "CREATE JAVA";
$cmdarray[161]= "ALTER JAVA";
$cmdarray[162]= "DROP JAVA";
$cmdarray[163]= "CREATE OPERATOR";
$cmdarray[164]= "CREATE INDEXTYPE";
$cmdarray[165]= "DROP INDEXTYPE";
$cmdarray[167]= "DROP OPERATOR";
$cmdarray[168]= "ASSOCIATE STATISTICS";
$cmdarray[169]= "DISASSOCIATE STATISTICS";
$cmdarray[170]= "CALL METHOD";
$cmdarray[171]= "CREATE SUMMARY";
$cmdarray[172]= "ALTER SUMMARY";
$cmdarray[173]= "DROP SUMMARY";
$cmdarray[174]= "CREATE DIMENSION";
$cmdarray[175]= "ALTER DIMENSION";
$cmdarray[176]= "DROP DIMENSION";
$cmdarray[177]= "CREATE CONTEXT";
$cmdarray[178]= "DROP CONTEXT";
$cmdarray[179]= "ALTER OUTLINE";
$cmdarray[180]= "CREATE OUTLINE";
$cmdarray[181]= "DROP OUTLINE";
$cmdarray[182]= "UPDATE INDEXES";
$cmdarray[183]= "ALTER OPERATOR";
?>
