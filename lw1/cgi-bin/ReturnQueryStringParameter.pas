PROGRAM GetParametrs(INPUT, OUTPUT);

USES
  GPC;

FUNCTION GetQueryStringParameter(Key: STRING): STRING;
VAR
  PosKey, PosMark: INTEGER;
  QueryString, QueryStringCop, QueryStringBef: STRING;
BEGIN
  QueryString := GetEnv('QUERY_STRING');
  PosKey := Pos(Key, QueryString);
  QueryStringCop := Copy(QueryString, PosKey, Length(QueryString) - PosKey + 1);
  PosMark := Pos('&', QueryStringCop);
  QueryStringBef := Copy(QueryString, PosKey - 1, 1);
  
  IF (QueryStringBef = '') OR (QueryStringBef = '&')
  THEN
    BEGIN
      IF PosMark <> 0
      THEN
        QueryString := Copy(QueryStringCop, Length(Key) + 1, PosMark - Length(Key) - 1)
      ELSE
        QueryString := Copy(QueryStringCop, Length(Key) + 1, Length(QueryStringCop) - Length(Key));
       
      GetQueryStringParameter := QueryString
    END
  ELSE
    GetQueryStringParameter := ''
END;

BEGIN {WorkWithQueryString}
  WRITELN('Content-Type: text/plain');
  WRITELN;
  WRITELN('First Name: ', GetQueryStringParameter('first_name='));
  WRITELN('Last Name: ', GetQueryStringParameter('last_name='));
  WRITELN('Age: ', GetQueryStringParameter('age='))
END. {WorkWithQueryString}
