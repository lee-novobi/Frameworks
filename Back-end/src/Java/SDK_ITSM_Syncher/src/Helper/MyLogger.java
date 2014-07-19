package Helper;

import java.io.*;
import java.text.DateFormat;
import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.logging.*;

// Referenced classes of package Helper:
//            Config

public class MyLogger
{

    public MyLogger()
    {
    }

    public static void WriteLog(Level logType, String content, String fileName, String filePath)
    {
        Logger logger = Logger.getLogger("MyLog");
        try
        {
            String fullpath = String.format("%s%s", new Object[] {
                filePath, fileName
            });
            FileHandler fh = new FileHandler(fullpath, true);
            logger.addHandler(fh);
            logger.setLevel(Level.ALL);
            SimpleFormatter formatter = new SimpleFormatter();
            fh.setFormatter(formatter);
            logger.log(logType, content);
        }
        catch(SecurityException e)
        {
            e.printStackTrace();
        }
        catch(IOException e)
        {
            e.printStackTrace();
        }
    }

    public static void WriteLog1(Level logType, String content)
    {
        Logger logger = Logger.getLogger("MyLog");
        try
        {
            DateFormat dfm = new SimpleDateFormat("yyyyMMdd");
            DateFormat dtfm = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
            String fileName = (new StringBuilder("log_")).append(dfm.format(new Date())).toString();
            String fullpath = String.format("%s%s", new Object[] {
                Config.LOG_PATH, fileName
            });
            FileHandler fh = new FileHandler(fullpath, true);
            logger.addHandler(fh);
            logger.setLevel(Level.ALL);
            SimpleFormatter formatter = new SimpleFormatter();
            fh.setFormatter(formatter);
            String logtime = (new StringBuilder(String.valueOf(dtfm.format(new Date())))).append(" --> ").toString();
            logger.log(logType, String.format("%s %s\n", new Object[] {
                logtime, content
            }));
        }
        catch(SecurityException e)
        {
            e.printStackTrace();
        }
        catch(IOException e)
        {
            e.printStackTrace();
        }
    }

    public static void WriteLog(String type, String content)
    {
        try
        {
            DateFormat dfm = new SimpleDateFormat("yyyyMMdd");
            DateFormat dtfm = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
            String fileName = (new StringBuilder("log_")).append(dfm.format(new Date())).toString();
            String fullpath = String.format("%s%s", new Object[] {
                Config.LOG_PATH, fileName
            });
            Writer output = null;
            File file = new File(fullpath);
            output = new BufferedWriter(new FileWriter(file, true));
            String logtime = String.format("%s --> %s: ", new Object[] {
                dtfm.format(new Date()), type
            });
            output.write(String.format("%s %s\n", new Object[] {
                logtime, content
            }));
            output.close();
        }
        catch(Exception e)
        {
            e.printStackTrace();
        }
    }
}
