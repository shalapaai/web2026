PROGRAM GetParametrs(INPUT, OUTPUT);

USES
  GPC;
  
VAR
  SarahsResponse: STRING;

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


BEGIN
  WRITELN('Content-Type: text/plain');
  WRITELN;
  SarahsResponse := GetQueryStringParameter('lanterns=');
  IF SarahsResponse = '1'
  THEN
    WRITELN('The British are coming by land.')
  ELSE
    IF SarahsResponse = '2'
    THEN
      WRITELN('The British are coming by sea.')
    ELSE
      WRITELN('Sarah didn''t say')
END.
