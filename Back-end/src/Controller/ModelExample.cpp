#include <iostream>
#include <memory>

#include <mongo/client/dbclient.h>

using namespace std;
using namespace mongo;

class MyModel
{
	public:
		MyModel(unsigned int uiE0, const string& strE1)
			: m_uiE0(uiE0),
			  m_strE1(strE1)
		{
		}

		void SetE0(unsigned int uiE0)
		{
			m_uiE0 = uiE0;
		}

		void SetE1(const string& strE1)
		{
			m_strE1 = strE1;
		}

	protected:
		unsigned int m_uiE0;
		string       m_strE1;
};

class MyMongoModel : public MyModel, public Model
{
	public:
		static const char *E0;
		static const char *E1;

		MyMongoModel(unsigned int uiE0, const string& strE1)
			: MyModel(uiE0, strE1)
		{
		}

        virtual string modelServer()
		{
			return "localhost";
		}

        virtual const char* getNS()
		{
			return "TestModel.Data";
		}

        virtual void serialize(BSONObjBuilder& to)
		{
			to << E0 << m_uiE0 << E1 << m_strE1;
		}

        virtual void unserialize(const BSONObj& from)
		{
			int i;
			from[E0].Val(i);
			m_uiE0 = i;
			from[E1].Val(m_strE1);
		}

		friend ostream& operator <<(ostream& out, MyMongoModel& mmm)
		{
			out << "C++: {" << MyMongoModel::E0 << " : " << mmm.m_uiE0 << ", " << MyMongoModel::E1 << " : " << mmm.m_strE1 << "}";
		}
};

const char *MyMongoModel::E0 = "E0";
const char *MyMongoModel::E1 = "E1";


void Print(DBClientConnection& c, const char *strNameSpace)
{
	cout << "MongoDB NameSpace: " << strNameSpace << endl;
	cout << " Doc Count: " << c.count(strNameSpace) << endl;
	auto_ptr<DBClientCursor> cur = c.query(strNameSpace, BSONObj());
	cout << " Docs:" << endl;
	while(cur->more()) {
		cout << " " << cur->next().toString() << endl;
	}
}

int main()
{
	try {
		DBClientConnection c;
		c.connect("localhost");

		unsigned int i = ~0;
		MyMongoModel mmm(i, "Text Data");

		cout << "Original data in C++ object:" << endl << mmm << endl << endl;

		mmm.save(true);
		cout << "After saving data in C++ object to MongoDB:" << endl;
		Print(c, mmm.getNS());
		cout << endl << mmm << endl << endl << endl;

		mmm.SetE0(100);
		mmm.SetE1("Modified Text Data");
		cout << "After changing data in C++ object:" << endl;
		cout << mmm << endl << endl << endl;

		BSONObj o = BSON(MyMongoModel::E0 << i);
		if(mmm.load(o)) {
			cout << "After loading data into C++ object from MongoDB:" << endl;
			Print(c, mmm.getNS());
			cout << endl << mmm << endl << endl << endl;
		}

		c.remove(mmm.getNS(), BSON(MyMongoModel::E0 << i));
		cout << "After removing data from MongoDB:" << endl;
		Print(c, mmm.getNS());
		cout << endl << mmm << endl;
	} catch(DBException& e) {
		cout << "Exception: " << e.what() << endl;
	}

	return 0;
}

